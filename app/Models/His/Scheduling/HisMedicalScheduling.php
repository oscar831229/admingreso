<?php

namespace App\Models\His\Scheduling;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use DB;

class HisMedicalScheduling extends Model
{
    protected $connection = 'HIS';

    protected $table = 'AGAGEMEDC';

    protected $fillable = [''];

    public static function getMedicalSchedulingFilters($filters){

        $start_date = isset($filters['start_date']) ? $filters['start_date'] : 0;
        $end_date = isset($filters['end_date']) ? $filters['end_date'] : 0;
        $specialty_code = isset($filters['specialty_code']) ? $filters['specialty_code'] : 0;
        $professional_code = isset($filters['professional_code']) ? $filters['professional_code'] : 0;

        $querySQL = "WITH agenda  AS (
            SELECT
                dbo.AGAGEMEDC.CODAUTONU,
                CAST(dbo.AGAGEMEDC.FECHORAIN AS date) AS FECHA,    
                dbo.AGAGEMEDC.FECHORAIN,
                dbo.AGAGEMEDC.FECHORAFI,
                CAST(dbo.AGAGEMEDC.CODESPECI AS CHAR(20)) AS CODESPECI,
                dbo.INESPECIA.DESESPECI,
                CAST(dbo.AGAGEMEDC.CODPROSAL AS CHAR(20)) AS CODPROSAL,
                dbo.INPROFSAL.NOMMEDICO
            FROM dbo.AGAGEMEDC 
            INNER JOIN dbo.INESPECIA ON dbo.AGAGEMEDC.CODESPECI = dbo.INESPECIA.CODESPECI 
            INNER JOIN dbo.INPROFSAL ON dbo.AGAGEMEDC.CODPROSAL = dbo.INPROFSAL.CODPROSAL 
            INNER JOIN dbo.AGCONSULT ON dbo.AGAGEMEDC.CODIGOCON = dbo.AGCONSULT.CODIGOCON
            WHERE  CAST(dbo.AGAGEMEDC.FECHORAIN AS DATE) BETWEEN ? AND ?
                AND (dbo.AGAGEMEDC.CODESPECI = '{$specialty_code}' OR '0' = '{$specialty_code}')
                AND (dbo.AGAGEMEDC.CODPROSAL = '{$professional_code}' OR '0' = '{$professional_code}')
        ), citas_agendadas AS (
            SELECT 
                AGAGEMEDC.*,
                dbo.AGASICITA.CODAUTONU AS AGASICITAID,
                dbo.AGASICITA.FECHORAIN AS FECHORAINCITA,
                dbo.AGASICITA.FECHORAFI AS FECHORAFICITA,
                DATEDIFF (MINUTE, dbo.AGASICITA.FECHORAIN, dbo.AGASICITA.FECHORAFI) AS MINUTOSCITA,
                dbo.AGASICITA.IPCODPACI,
                dbo.INPACIENT.IPNOMCOMP,
                dbo.AGASICITA.CODTIPCIT,
                CASE
                    WHEN dbo.AGASICITA.CODTIPCIT = 0 THEN 'PRIMERA VEZ'
                    WHEN dbo.AGASICITA.CODTIPCIT = 1 THEN 'CONTROL'
                    WHEN dbo.AGASICITA.CODTIPCIT = 2 THEN 'POS OPERATIORIO'
                    ELSE ''
                END AS CODTIPCITDES,
                dbo.AGASICITA.CODESTCIT,
                CASE
                    WHEN dbo.AGASICITA.CODESTCIT = 0 THEN 'ASIGNADA'
                    WHEN dbo.AGASICITA.CODESTCIT = 1 THEN 'CUMPLIDA'
                    WHEN dbo.AGASICITA.CODESTCIT = 2 THEN 'INCUMPLIDA'
                    WHEN dbo.AGASICITA.CODESTCIT = 3 THEN 'PRE ASIGNADA'        	
                    ELSE ''
                END AS CODESTCITDES,
                dbo.AGASICITA.OBSERVACI,
                dbo.AGASICITA.CODUSUASI,
                dbo.SEGusuaru.NOMUSUARI,
                dbo.AGASICITA.FECREGSIS
            FROM agenda AS AGAGEMEDC 
            LEFT JOIN dbo.AGASICITA ON AGAGEMEDC.CODESPECI = AGASICITA.CODESPECI
                      AND AGAGEMEDC.CODPROSAL = DBO.AGASICITA.CODPROSAL
                      AND dateadd(MINUTE, -1, DBO.AGASICITA.FECHORAFI) BETWEEN AGAGEMEDC.FECHORAIN AND AGAGEMEDC.FECHORAFI
            LEFT JOIN dbo.SEGusuaru ON dbo.SEGusuaru.CODUSUARI = dbo.AGASICITA.CODUSUASI
            LEFT JOIN dbo.INPACIENT ON dbo.INPACIENT.IPCODPACI = dbo.AGASICITA.IPCODPACI
                AND AGASICITA.CODPROSAL = AGAGEMEDC.CODPROSAL 
                AND dateadd(MINUTE, -1, DBO.AGASICITA.FECHORAFI) BETWEEN AGAGEMEDC.FECHORAIN AND AGAGEMEDC.FECHORAFI 
            WHERE DBO.AGASICITA.CODESTCIT <> 4
        )
        
        SELECT * FROM citas_agendadas";

        # AGENDAS PROGRAMADAS
        $results = DB::connection('HIS')->select($querySQL, [
            $start_date,
            $end_date
        ]);
    
        $schedulings = [];
        foreach ($results as $key => $result) {

            if(!isset($schedulings[$result->CODAUTONU])){
                $schedulings[$result->CODAUTONU] = [
                    'id' => $result->CODAUTONU,
                    'date' => $result->FECHA,
                    'start_date_time' => $result->FECHORAIN,
                    'end_date_time' => $result->FECHORAFI,
                    'specialty_code' => trim($result->CODESPECI),
                    'specialty_name' => trim($result->DESESPECI),
                    'professional_code' => trim($result->CODPROSAL),
                    'professional_name' => trim($result->NOMMEDICO),
                    'scheduled_appointments' => []
                ];
            }

            # CITAS PROGRAMADAS
            if(!empty($result->AGASICITAID)){

                $scheduled_appointment = [
                    'scheduled_appointment_id' => $result->AGASICITAID,
                    'start_date_time' => $result->FECHORAINCITA,
                    'end_date_time' => $result->FECHORAFICITA,
                    'time_minutes' => $result->MINUTOSCITA,
                    'patient_code' => trim($result->IPCODPACI),
                    'patient_name' => trim($result->IPNOMCOMP),
                    'appointment_type_code' => trim($result->CODTIPCIT),
                    'appointment_type_name' => trim($result->CODTIPCITDES),
                    'appointment_state_code' => trim($result->CODESTCIT),
                    'appointment_state_name' => trim($result->CODESTCITDES),
                    'observation' => trim($result->OBSERVACI),
                    'user_created_code' => trim($result->CODUSUASI),
                    'user_created_name' => trim($result->NOMUSUARI),
                    'created_at' => $result->FECREGSIS,
                ];

                $schedulings[$result->CODAUTONU]['scheduled_appointments'][] = $scheduled_appointment;

            }
            
        }

        return  array_values($schedulings);

    }


    public static function getSpecialty($year){

        $querySQL = "SELECT
                DISTINCT 
                LTRIM(RTRIM(dbo.AGAGEMEDC.CODESPECI)) AS CODESPECI,
                dbo.INESPECIA.DESESPECI
            FROM dbo.AGAGEMEDC 
            INNER JOIN dbo.INESPECIA ON dbo.AGAGEMEDC.CODESPECI = dbo.INESPECIA.CODESPECI 
            INNER JOIN dbo.INPROFSAL ON dbo.AGAGEMEDC.CODPROSAL = dbo.INPROFSAL.CODPROSAL 
            INNER JOIN dbo.AGCONSULT ON dbo.AGAGEMEDC.CODIGOCON = dbo.AGCONSULT.CODIGOCON
            WHERE  CONVERT(VARCHAR(4),dbo.AGAGEMEDC.FECHORAIN, 112) = ?";

        $specialty = DB::connection('HIS')->select($querySQL, [$year]);

        $response = [];
        foreach ($specialty as $key => $result) {
            $response[] = [
                'specialty_code' => trim($result->CODESPECI),
                'specialty_name' => trim($result->DESESPECI)
            ];
        }

        return $response;

    }   

    public static function getProfessional($year){

        $querySQL = "SELECT
                DISTINCT 
                LTRIM(RTRIM(dbo.AGAGEMEDC.CODPROSAL)) AS CODPROSAL,
                dbo.INPROFSAL.NOMMEDICO,
                LTRIM(RTRIM(dbo.AGAGEMEDC.CODESPECI)) AS CODESPECI,
                dbo.INESPECIA.DESESPECI
            FROM dbo.AGAGEMEDC 
            INNER JOIN dbo.INESPECIA ON dbo.AGAGEMEDC.CODESPECI = dbo.INESPECIA.CODESPECI 
            INNER JOIN dbo.INPROFSAL ON dbo.AGAGEMEDC.CODPROSAL = dbo.INPROFSAL.CODPROSAL 
            INNER JOIN dbo.AGCONSULT ON dbo.AGAGEMEDC.CODIGOCON = dbo.AGCONSULT.CODIGOCON
            WHERE  CONVERT(VARCHAR(4),dbo.AGAGEMEDC.FECHORAIN, 112) = ?";

        $professional = DB::connection('HIS')->select($querySQL, [$year]);

        $response = [];

        foreach ($professional as $key => $result) {
            $response[] = [
                'professional_code' => trim($result->CODPROSAL),
                'professional_name' => trim($result->NOMMEDICO),
                'specialty_code' => trim($result->CODESPECI),
                'specialty_name' => trim($result->DESESPECI)
            ];
        }

        return $response;


    }

}

