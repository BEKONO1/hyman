{{ html()->form('POST', route('heading_page_settings'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('myForm')->open() }}

{{ html()->hidden('id',$landing_page_data)->placeholder('id')->class('form-control') }}
{{ html()->hidden('type', $page)->placeholder('id')->class('form-control') }}

    <div class="form-group">
        <div class="form-control d-flex align-items-center justify-content-between">
            <label for="enable_header_setting" class="mb-0">{{__('messages.enable_header_setting')}}</label>
            <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <input type="checkbox" class="custom-control-input header_setting" name="status" id="header_setting" data-type="header_setting" {{ !empty($landing_page_data) && $landing_page_data->status ? 'checked' : '' }}>
                <label class="custom-control-label" for="header_setting"></label>
            </div>
        </div>
    </div>

<div class="row" id='enable_header_setting'>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body setting-pills">
                <div class="row">
                    <div class="col-sm-12 col-lg-12">
                    @if(!empty($landing_page_data) && $landing_page_data->status)
                    
                           @php
                                $valueArray = json_decode($landing_page_data->value ?? '{}', true);
                                $availableKeys = ['service', 'provider', 'categories', 'bookings'];
                            @endphp
                            
                            <ul class="nav flex-column nav-pills nav-fill tabslink list" id="tabs-text" role="tablist">
                                @foreach($availableKeys as $key)
                                    @php $value = $valueArray[$key] ?? 0; @endphp
                                    <li class="nav-item list-item" data-section="{{ $key }}" draggable=true>
                                        <div class="form-group mb-0">
                                            <div class="form-control d-flex align-items-center justify-content-between">
                                                <label for="{{ $key }}" class="mb-0">{{ __('messages.' . $key) }}</label>
                                                <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                                                    <input type="checkbox" class="custom-control-input" name="{{ $key }}" id="{{ $key }}" {{ $value ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="{{ $key }}"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                        <ul class="nav flex-column nav-pills nav-fill tabslink list" id="tabs-text" role="tablist">

                            <li class="nav-item list-item" data-section="service" draggable=true>
                                <div class="form-group mb-0">
                                    <div class="form-control d-flex align-items-center justify-content-between">
                                        <label for="service" class="mb-0">{{ __('messages.services') }}</label>
                                        <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" name="service" id="service" {{ !empty($landing_page_data->service) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="service"></label>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li class="nav-item list-item" data-section="provider" draggable=true>
                                <div class="form-group mb-0">
                                    <div class="form-control d-flex align-items-center justify-content-between">
                                        <label for="provider" class="mb-0">{{ __('messages.providers') }}</label>
                                        <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" name="provider" id="provider" {{ !empty($landing_page_data->provider) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="provider"></label>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li class="nav-item list-item" data-section="categories" draggable=true>
                                <div class="form-group mb-0">
                                    <div class="form-control d-flex align-items-center justify-content-between">
                                        <label for="categories" class="mb-0">{{ __('messages.categories') }}</label>
                                        <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" name="categories" id="categories" {{ !empty($landing_page_data->categories) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="categories"></label>
                                        </div>
                                    </div>
                                </div>
                            </li>
                             
                            <li class="nav-item list-item" data-section="bookings" draggable=true>
                                <div class="form-group mb-0">
                                    <div class="form-control d-flex align-items-center justify-content-between">
                                        <label for="blogs" class="mb-0">{{ __('messages.bookings') }}</label>
                                        <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" name="bookings" id="bookings" {{ !empty($landing_page_data->bookings) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="bookings"></label>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                        </ul>
                        @endif
                        <div class="form-padding-box mt-3">
                            <div class="form-group">
                                <div class="form-control d-flex align-items-center justify-content-between flex-wrap gap-1">
                                    <label for="enable_language" class="mb-0">{{__('messages.enable_language')}}</label>
                                    <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                                        <input type="checkbox" class="custom-control-input" name="enable_language" id="enable_language" {{ !empty($landing_page_data->enable_language) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="enable_language"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <div class="form-control d-flex align-items-center justify-content-between flex-wrap gap-1">
                                    <label for="enable_darknight_mode" class="mb-0">{{__('messages.enable_darknight_mode')}}</label>
                                    <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                                        <input type="checkbox" class="custom-control-input" name="enable_darknight_mode" id="enable_darknight_mode" {{ !empty($landing_page_data->enable_darknight_mode) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="enable_darknight_mode"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end') }}
{{ html()->form()->close() }}

<script>
        var enable_header_setting = $("input[name='status']").prop('checked');
    checkSection1(enable_header_setting);

    $('#header_setting').change(function () {
        value = $(this).prop('checked') == true ? true : false;
        checkSection1(value);
    });

    function checkSection1(value) {
        if (value == true) {
            $('#enable_header_setting').removeClass('d-none');
        } else {
            $('#enable_header_setting').addClass('d-none');
        }
    }

    const listItems = document.querySelectorAll('.list-item');

    listItems.forEach(function (item) {
        item.addEventListener('dragstart', function (e) {
            e.dataTransfer.setData('text', e.target.id);
        });

        item.addEventListener('dragover', function (e) {
            e.preventDefault();
            const dragging = document.querySelector('.dragging');
            const bounding = this.getBoundingClientRect();
            const offset = e.clientY - bounding.top - bounding.height / 2;
            const next = offset > 0 ? this : this.previousElementSibling;

            if (next && dragging !== next) {
                dragging.parentNode.removeChild(dragging);
                next.parentNode.insertBefore(dragging, offset > 0 ? next.nextSibling : next);
            }
        });
    });

    document.addEventListener('dragstart', function (e) {
        if (e.target.classList.contains('list-item')) {
            e.target.classList.add('dragging');
        }
    });

    document.addEventListener('dragend', function (e) {
        if (e.target.classList.contains('list-item')) {
            e.target.classList.remove('dragging');
            updateOrder();
        }
    });

    function updateOrder() {
        const sections = ['home', 'service', 'provider', 'categories', 'blogs'];
        const order = {};
        sections.forEach(function (section) {
            const sectionItems = Array.from(document.querySelectorAll(`.list-item[data-section="${section}"]`));
            const itemOrder = sectionItems.map(function (item) {
                return item.id;
            });
            order[section] = itemOrder;
        });

        // console.log(order); 
    }
    $('#myForm').submit(function () {
        $(':checkbox').each(function () {
            $(this).after('<input type="hidden" name="' + this.name + '" value="' + ($(this).is(':checked') ? 'on' : 'off') + '">');
        });
    });

    document.addEventListener('DOMContentLoaded', (event) => {
    const draggables = document.querySelectorAll('.list-item');
    const container = document.querySelector('.list');

    draggables.forEach(draggable => {
        draggable.addEventListener('dragstart', () => {
            draggable.classList.add('dragging');
        });

        draggable.addEventListener('dragend', () => {
            draggable.classList.remove('dragging');
        });
    });

    container.addEventListener('dragover', (e) => {
        e.preventDefault();
        const afterElement = getDragAfterElement(container, e.clientY);
        const draggingElement = document.querySelector('.dragging');
        if (afterElement == null) {
            container.appendChild(draggingElement);
        } else {
            container.insertBefore(draggingElement, afterElement);
        }
    });
});

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.list-item:not(.dragging)')];

    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

</script>