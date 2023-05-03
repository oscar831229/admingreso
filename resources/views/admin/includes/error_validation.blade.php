@if (count($errors) > 0)
  <div class="alert alert-danger col-xs-12 ml-4 mt-3 mr-3">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <strong>Error</strong> Faltan datos por ingresar.<br><br>
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
}@endif