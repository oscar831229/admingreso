<?php

namespace App\Clases\ElectronicWallet;

use App\Models\Wallet\ConsecutiveTicket;
use App\Models\Wallet\WalletUserTicket;

use Illuminate\Support\Str;



class TicketHelperClass
{

    public function __construct($movement){
        $this->movement = $movement;
    }

    public function setTransaction(){

    }

    public function runValidation(){

        # VALIDACIONES ABONOS
        if($this->movement->movement_type_code == '01'  && $this->movement->electronic_pocket->operation_type == 'T'){
            
            # VALIDAR COMPRA MINIMA
            $minimum_purchase_value = $this->movement->electronic_pocket->minimum_purchase *  $this->movement->electronic_pocket->unit_value;

            if($this->movement->transaction->value < $minimum_purchase_value){
                throw new \Exception("Error la compra minima de tiquetera es de (".number_format($minimum_purchase_value,2).")", 1);
            }
                
                
            # VALIDAR VALOR QUE SEAN MULTIPLSO DEL VALOR UNITARIO
            if($this->movement->transaction->value%$this->movement->electronic_pocket->unit_value != 0)
                throw new \Exception("Error la compra de tiquetera deber concidir con Cantidad tiquetes * valor unidad tiquete ($".number_format($this->movement->electronic_pocket->unit_value).")", 1);

        }

        # VALIDACIONES ABONOS
        if($this->movement->movement_type_code == '02'  && $this->movement->movement_parent->electrical_pocket_operation_type == 'T'){

            $this->tickets = WalletUserTicket::lockForUpdate()->where(['movement_id' => $this->movement->movement_parent->id])->get();
            $total_value = 0;
            foreach ($this->tickets as $key => $ticket) {

                # Validar estado del ticket
                if($ticket->state != 'P')
                    throw new \Exception("Error el número de ticket {$ticket->number_ticket} no tiene un estado valido. Estado actual({$ticket->state})", 1);

                $total_value += $ticket->value;
                
            }

            $resuido = $this->movement->movement_parent->value - $total_value;
            if($resuido > 0 || $resuido < 0){
                throw new \Exception("Erro en la transacción, no conciliacion valor transacción vs valor tickets", 1);
            }

        }

        # CONSUMOS TIQUETERA 
        if($this->movement->movement_type_code == '03'  && $this->movement->electronic_pocket->operation_type == 'T'){

            # VALIDAR QUE EXISTAN TICKETS A REDIMIR
            if(!is_array($this->movement->transaction->tickets) || !isset($this->movement->transaction->tickets)){
                throw new \Exception("Error no se ha indicado los ticket a redimir.", 1);
            }

            # VALIDAR
            if(count($this->movement->transaction->tickets) == 0){
                throw new \Exception("Error no exiten tickets para redimir.", 1);
            }

            # VERIFICAR QUE TODOS LOS TICKETS EXISTAN CON EL USUARIO
            $tickets_all = $this->movement->transaction->tickets;
            $duplication_control = [];
            $total_value = 0;

            # CONTROLAR TICKES DUPLICADOS
            foreach ($tickets_all as $key => $value) {
                # Controlar duplicidad
                if(isset($duplication_control[$value]))
                    throw new \Exception("Error el número de ticket {$value} esta duplicado en la transacción.");

                $duplication_control[$value] = 1;
            }

            #CONSULTAR TABLA CONSECUTIVOS
            $this->tickets =  WalletUserTicket::lockForUpdate()->where(['wallet_user_id' => $this->movement->wallet_user->id])->whereIn('number_ticket',$this->movement->transaction->tickets)->get();
            foreach ($this->tickets as $key => $ticket) {

                # Validar estado del ticket
                if($ticket->state != 'P')
                    throw new \Exception("Error el número de ticket {$ticket->number_ticket} no tiene un estado valido. Estado actual({$ticket->state})", 1);

                # VERIFICAR INDICE
                $find_index = array_search($ticket->number_ticket, $tickets_all);
                unset($tickets_all[$find_index]);

                $total_value += $ticket->value;
                
            }

            if(count($tickets_all) > 0 ){
                $string_tickets = implode(', ', $tickets_all);
                throw new \Exception("En la transacción de consumo existes tickets que no pertencen al usuario tickets({$string_tickets})", 1);
            }

            $resuido = $this->movement->transaction->value - $total_value;
            if($resuido > 0 || $resuido < 0){
                throw new \Exception("Erro en la transacción, no conciliacion valor transacción vs valor tickets", 1);
            }
            
        }

        
        # VALIDACION REVERSOS CONSUMO TIQUETERA ELECTRONICA
        if($this->movement->movement_type_code == '04'  && $this->movement->movement_parent->electrical_pocket_operation_type == 'T'){
            
            $this->tickets = WalletUserTicket::lockForUpdate()->where(['state_movement_id' => $this->movement->movement_parent->id])->get();
            $this->movement->setTickets($this->tickets);


            $total_value = 0;
            foreach ($this->tickets as $key => $ticket) {

                # Validar estado del ticket
                if($ticket->state != 'R')
                    throw new \Exception("Error el número de ticket {$ticket->number_ticket} no tiene un estado valido. Estado actual({$ticket->state})", 1);

                $total_value += $ticket->value;
                
            }

            $resuido = $this->movement->movement_parent->value - $total_value;
            if($resuido > 0 || $resuido < 0){
                throw new \Exception("Erro en la transacción, no conciliacion valor transacción vs valor tickets", 1);
            }
            
        }

    }

    public function generateTickets($movement_register){

        

        # SI EL PROCESO ES DE ABONO - GENERA CONSECUTIVOS
        if($this->movement->movement_type_code == '01'  && $this->movement->electronic_pocket->operation_type == 'T'){
            
            # Asignar consecutivo tiquetera
            $this->loadConsecutiveTicket();

            $str_length = Str::length($this->consecutive->final_consecutive);
            $prefix = trim($this->consecutive->prefix);

            $rows_ticket = $movement_register->value / $this->movement->electronic_pocket->unit_value;

            for ($i= 0; $i < (int) $rows_ticket; $i++) {

                # CONSECUTIVO
                $consecutive = empty($this->consecutive->current_consecutive) ? $this->consecutive->initial_consecutive : $this->consecutive->current_consecutive;
                $number_ticket = $prefix.str_pad($consecutive, $str_length, "0", STR_PAD_LEFT);
                
                # GRABAR TICKET
                WalletUserTicket::create([
                    'wallet_user_id' => $movement_register->wallet_user_id,
                    'movement_id' => $movement_register->id,
                    'consecutive_ticket_id' => $this->consecutive->id,
                    'number' => $consecutive,
                    'number_ticket' => $number_ticket,
                    'value' => $this->movement->electronic_pocket->unit_value,
                    'state' => 'P',
                    'user_created' => auth()->user()->id
                ]);

                # INCREMENTAR CONSECUTIVO
                $this->consecutive->current_consecutive = $consecutive+1;
                
            }

            $this->consecutive->update();

        }

        # VALIDACION REVERSOS CONSUMO TIQUETERA ELECTRONICA
        if($this->movement->movement_type_code == '02'  && $this->movement->movement_parent->electrical_pocket_operation_type == 'T'){
            foreach ($this->tickets as $key => $ticket) {
                $ticket->state = 'A';
                $ticket->state_movement_id = $movement_register->id;
                $ticket->update();
            }
        }

        # CONSUMOS TIQUETERA
        if($this->movement->movement_type_code == '03'  && $this->movement->electronic_pocket->operation_type == 'T'){
            foreach ($this->tickets as $key => $ticket) {
                $ticket->state = 'R';
                $ticket->state_movement_id = $movement_register->id;
                $ticket->update();
            }
        }

        # VALIDACION REVERSOS CONSUMO TIQUETERA
        if($this->movement->movement_type_code == '04'  && $this->movement->movement_parent->electrical_pocket_operation_type == 'T'){
            foreach ($this->tickets as $key => $ticket) {
                $ticket->state = 'P';
                $ticket->state_movement_id = null;
                $ticket->update();
            }
        }

        
        
    }

    public function loadConsecutiveTicket(){

        #CONSULTAR TABLA CONSECUTIVOS
        $this->consecutive =  ConsecutiveTicket::lockForUpdate()->where(['state' => 'A'])->whereRaw("'".date('Y-m-d')."' between date_from AND date_to")->first();
        
        if(!$this->consecutive)
            throw new Exception("Error no existe consecutivo para la tiquetera definido.", 1);
            

    }
}