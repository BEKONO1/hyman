@foreach($buttonTypes as $key => $value)
<button type="button" class="btn btn-primary-subtle text-capitalize border-0 mt-2 mx-1" id="variable_button" data-value="{{ '[[ '.$value->value.' ]]' }}">{{ $value->name }}</button>
@endforeach
