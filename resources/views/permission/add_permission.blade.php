<!-- Modal -->

<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ $title }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"></span>
            </button>
        </div>
        {{ html()->form('POST', route('permission.save'))->attribute('data-toggle', 'validator')->open() }}
    <div class="modal-body">
    
        {{ html()->hidden('type', $type) }}
        {{ html()->hidden('id', -1) }}
            <div class="row">
                <div class="col-md-12 form-group">
                    {{ html()->label(trans('messages.name').' <span class="text-danger">*</span>', 'name', ['class' => 'form-control-label'], false) }}
                    {{ html()->text('name', null)->placeholder(trans('messages.name'))->class('form-control')->required() }}    
                </div>
            </div>
            @if( $type == 'permission' )
                <div class="row">
                    <div class="col-md-12 form-group">
                        {{ html()->label(trans('messages.parent_permission'), 'parent_id', ['class' => 'form-control-label']) }}
                        <select name="parent_id" id="parent_id" class="select2js form-control" data-ajax--url="{{ route('ajax-list', ['type' => 'permission']) }}" data-ajax--cache = "true">
                       
                    </select>
    </div>
                </div>
            @endif
        </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-md btn-secondary" data-bs-dismiss="modal">{{ trans('messages.close') }}</button>
            <button type="submit" class="btn btn-md btn-primary" id="btn_submit" data-form="ajax" >{{ trans('messages.save') }}</button>
    </div>
{{ html()->form()->close() }}   
    </div>
</div>
<script>
    $('#parent_id').select2({
        width: '100%',
        placeholder: "{{ trans('messages.select_name',['select' => trans('messages.parent_permission')]) }}",
    });
</script>

