{{--  Modal especialidades  --}}
<div class="modal fullscreen-modal modal-months fade" id="md-specialty" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header text-white bg-primary">
        <h6 class="modal-title" id="exampleModalLabel"><i class="fa fa-modx mr-2" aria-hidden="true"></i>Especialidades</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" >
        <div class="row mb-2">
          <div class="input-group col-md-12">
            <input class="form-control py-2" type="search" value="" placeholder="buscar" id="search-input">
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <div class="well" style="max-height: 300px;overflow: auto;">
              <ul class="list-group checked-list-box list-speciality" data-alias="chk-especiality">
                <li class="list-group-item check-todos" data-checked="true" data-value="0">Todos</li>
                @foreach ($specialties as $key => $speciality)
                <li class="list-group-item" data-checked="true" data-value="{{ $key }}">{{ $speciality }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>
{{--  Fin modal especialidades  --}}