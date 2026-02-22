<!-- Backend Bundle JavaScript -->
 <script src="{{ asset('js/backend-bundle.min.js')}}"></script>
<script src="{{ asset('vendor/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('vendor/tinymce/js/tinymce/jquery.tinymce.min.js') }}"></script>
<link href="{{ asset('css/dragula.css') }}" rel="stylesheet">
<script src="{{ asset('js/dragula.min.js') }}"></script>
<script src="{{ asset('js/swiper-bundle.min.js') }}"></script>

<script>
      // Set the primary locale (default language)
   const primaryLanguageId = 'en';

   /**
    * Toggles the visibility of language-specific forms
    * and updates the active language button styling.
    * @param {string} languageId - The ID of the selected language.
    */
   function toggleLanguageForm(languageId) {
      // Hide all language forms
      document.querySelectorAll('.language-form').forEach(function(form) {
         form.style.display = 'none';
      });

      // Display the selected language form
      const selectedForm = document.getElementById('form-language-' + languageId);
      if (selectedForm) {
         selectedForm.style.display = 'block';
      }

      // Update button styles to indicate the active language
      document.querySelectorAll('.language-btn').forEach(function(btn) {
         btn.classList.remove('btn-primary');
         btn.classList.add('btn-outline-secondary');
      });

      const activeButton = document.querySelector(`.language-btn[onclick*="${languageId}"]`);
      if (activeButton) {
         activeButton.classList.add('btn-primary');
      }

      // Set required fields for the selected language form
      setRequiredFields(languageId);
   }

   /**
    * Updates the 'required' attribute for input fields
    * in the selected language form based on conditions.
    * @param {string} languageId - The ID of the selected language.
    */
   function setRequiredFields(languageId) {
      // Clear 'required' attribute from all inputs, selects, and textareas in all forms
      document.querySelectorAll('.language-form input, .language-form select, .language-form textarea').forEach(function(field) {
         field.removeAttribute('required');
      });
      document.querySelectorAll('.language-label span.text-danger').forEach(function (span) {
            span.style.display = 'none'; // Hide all * markers
    });

      // Apply 'required' attribute only for the active language form (default language)
      if (languageId === primaryLanguageId) {
         const activeLanguageForm = document.querySelector(`#form-language-${languageId}`);
         if (activeLanguageForm) {
               activeLanguageForm.querySelectorAll('[data-required="true"]').forEach(function(field) {
                  field.setAttribute('required', 'required');
               });
               activeLanguageForm.querySelectorAll('.language-label span.text-danger').forEach(function (span) {
                span.style.display = 'inline'; // Show * markers for English
            });
         }
      }
   }

   // Function to check if required fields in the active language form are filled
   function checkData() {
      let isValid = true;

      // Get the active language form based on the current language ID
      const activeLanguageForm = document.querySelector(`#form-language-${primaryLanguageId}`);
      
      if (activeLanguageForm) {
         const requiredFields = activeLanguageForm.querySelectorAll('input[required], select[required], textarea[required]');
         
         requiredFields.forEach(function(field) {
               if (!field.value.trim()) {
                  isValid = false;
                  showSnackbar(`Please complete the required fields in the active language form.`);
                  field.focus();
                  return false; // Stop further validation
               }
         });
      }

      return isValid;
   }

   /**
    * Displays a snackbar notification with the given message.
    * @param {string} message - The notification message to display.
    */
   function showSnackbar(message) {
      const snackbar = document.getElementById("snackbar");
      if (snackbar) {
         snackbar.textContent = message;
         snackbar.className = "show";
         setTimeout(function() {
               snackbar.className = snackbar.className.replace("show", "");
         }, 3000);
      }
   }

   // Initialize the form on page load
   document.addEventListener('DOMContentLoaded', function() {
      setRequiredFields(primaryLanguageId);
   });

</script>

<script>
 $(document).ready(function () {
    let totalServicePrice = 0;
    // Function to initialize Select2 for a given element
    function initializeSelect2($element) {
        const selectedId = $element.data('selected-id'); // Get the selected IDs (could be a single or multiple)
        const ajaxUrl = $element.data('ajax--url');
        const placeholder = $element.data('placeholder');
        // If selectedId is a string, treat it as a single selected ID, otherwise, assume it's an array of multiple IDs
        const selectedIds = typeof selectedId === 'string' ? selectedId.split(',') : (Array.isArray(selectedId) ? selectedId : []); 
        $element.select2({
            placeholder: placeholder,
            ajax: {
                url: ajaxUrl,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // Search term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.id, text: item.text, price: item.price };
                        }),
                    };
                },
                cache: true,
            },
        });

        if (selectedIds) {
            $.ajax({
                url: ajaxUrl, // Fetch the preselected item(s)
                data: { ids: selectedIds },
                dataType: 'json',
                success: function (response) {
                    // If multiple selected IDs are present, match all and append
                    if (Array.isArray(selectedIds) && selectedIds.length > 0) {


                        totalServicePrice = 0;
                        selectedIds.forEach(function (id) {
                            const selectedItem = response.find(item => item.id == id);
                            if (selectedItem) {
                                // Check if the option is already present to prevent duplication
                                const existingOption = $element.find(`option[value="${selectedItem.id}"]`);

                                if (existingOption.length === 0) {
                                    const option = new Option(selectedItem.text, selectedItem.id, true, true);
                                    $element.append(option);
                                    
                                    // Update DOM and total price only after appending
                                    if (selectedItem.price) {
                                        totalServicePrice += parseFloat(selectedItem.price || 0);
                                    }
                                    updateTotalPrice(totalServicePrice);
                                    
                                    // Trigger change to ensure Select2 updates and re-checks the DOM
                                    $element.trigger('change');
                                }
                            }
                        });
                        
                    } else if (typeof selectedId === 'string' || typeof selectedId === 'number') {
                        const selectedItem = response.find(item => item.id == selectedId);
                        if (selectedItem) {
                            const option = new Option(selectedItem.text, selectedItem.id, true, true);
                            const existingOption = $element.find(`option[value="${selectedItem.id}"]`);
                           
                            if (existingOption.length === 0) {
                                $element.append(option);
                                 // Update DOM and total price only after appending
                                 if (selectedItem.price) {
                                        totalServicePrice += parseFloat(selectedItem.price || 0);
                                    }
                                    updateTotalPrice(totalServicePrice);
                                    
                                    // Trigger change to ensure Select2 updates and re-checks the DOM
                                    $element.trigger('change');
                            }
                        }
                    }

                    // Trigger 'change' event to update Select2 dropdown
                    //$element.trigger('change');
                },
                error: function () {
                    if (selectedIds && selectedIds.length > 0) {
                        console.error('Failed to fetch selected item(s) for:', selectedIds);
                    }
                },
            });
        }
    }

    function updateTotalPrice(totalServicePrice) {
       
        $('#original_price').val(totalServicePrice.toFixed(2));
    }
   
    function synchronizeDropdowns(type, selectedIds, selectedId) {
        const processedIds = new Set();
        if (Array.isArray(selectedIds)) {
            selectedIds.forEach(function (id) {
                if (!processedIds.has(id)) {
                    processedIds.add(id);
                    updateDropdown(type, selectedId, id);
                }
            });
        } else {
            updateDropdown(type, null, selectedId);
        }
    }
    function updateDropdown(type, selectedIds,selectedId) {
    const $dropdowns = $(`.select2js-${type}`);

    // Loop through all dropdowns of the given type (single or multi-select)
    $dropdowns.each(function () {
        const $dropdown = $(this);

        // Fetch the translated value for the selected ID using AJAX
        $.ajax({
            url: $dropdown.data('ajax--url'),
            data: { id: selectedId },
            dataType: 'json',
            success: function (response) {
                const translatedItem = response.find(item => item.id == selectedId);
                if (translatedItem) {
                    const existingOption = $dropdown.find(`option[value="${translatedItem.id}"]`);

                    // Check if the option already exists
                    if (existingOption.length === 0) {
                        // Create a new option element
                        const option = new Option(translatedItem.text, translatedItem.id, true, true);
                        
                        // If it's a multi-select, append it, otherwise replace the existing options
                        if ($dropdown.prop('multiple')) {
                            $dropdown.append(option).trigger('change');
                        } else {
                            $dropdown.empty().append(option).trigger('change');
                        }
                    }

                    // Ensure selected ID is marked as selected in multi-select or single-select dropdown
                    if ($dropdown.prop('multiple')) {
                        $dropdown.val($dropdown.val().concat([translatedItem.id])).trigger('change');
                    } else {
                        $dropdown.val(translatedItem.id).trigger('change');
                    }
                }
            },
        });
    });
}
    // Function to update subcategory dropdown based on category selection
   // Function to update subcategory dropdown based on category selection
    function fetchSubcategoryList(categoryId, $subcategoryDropdown) {
        if (!categoryId) {
            $subcategoryDropdown.empty(); // Clear existing options
            return; // Exit the function
        }
        const subcategoryAjaxUrl = $subcategoryDropdown
            .data('ajax--url')
            .replace(/category_id=[^&]*/, `category_id=${categoryId}`);

        if ($subcategoryDropdown.hasClass('select2-hidden-accessible')) {
            $subcategoryDropdown.select2('destroy');
        }

        $subcategoryDropdown.empty();
        $subcategoryDropdown.data('ajax--url', subcategoryAjaxUrl);
        initializeSelect2($subcategoryDropdown);
    }

    function fetchServiceList(providerId, categoryId = null, subcategoryId = null, $serviceDropdown) {
        let ajaxUrl = $serviceDropdown.data('ajax--url');

        // Replace or append provider_id in the URL
        if (providerId) {
            ajaxUrl = ajaxUrl.includes('provider_id=')
                ? ajaxUrl.replace(/provider_id=[^&]*/, `provider_id=${providerId}`)
                : ajaxUrl + `&provider_id=${providerId}`;
        } 

        // Replace or append category_id in the URL
        if (categoryId) {
            ajaxUrl = ajaxUrl.includes('category_id=')
                ? ajaxUrl.replace(/category_id=[^&]*/, `category_id=${categoryId}`)
                : ajaxUrl + `&category_id=${categoryId}`;
        } 

        // Replace or append subcategory_id in the URL
        if (subcategoryId) {
            ajaxUrl = ajaxUrl.includes('subcategory_id=')
                ? ajaxUrl.replace(/subcategory_id=[^&]*/, `subcategory_id=${subcategoryId}`)
                : ajaxUrl + `&subcategory_id=${subcategoryId}`;
        } 
 
        $serviceDropdown.empty();
        $serviceDropdown.data('ajax--url', ajaxUrl);
        initializeSelect2($serviceDropdown);
    }

    function updateDropdowns($providerDropdown, $categoryDropdown, $subcategoryDropdown, $serviceDropdown) {
    // Handle provider change
        $providerDropdown.off('change').on('change', function () {
            const providerId = $(this).val();
            totalServicePrice = 0;
            updateTotalPrice(totalServicePrice);
            if(providerId){
                fetchServiceList(providerId, $categoryDropdown.val(), $subcategoryDropdown.val(), $serviceDropdown);
            }
            // Fetch service list with providerId
            
        });

        // Handle category change
        $categoryDropdown.off('change').on('change', function () {
            const categoryId = $(this).val();
            totalServicePrice = 0;
            updateTotalPrice(totalServicePrice);
            if(categoryId){
            // Update subcategory list
            if ($subcategoryDropdown.length) {
                fetchSubcategoryList(categoryId, $subcategoryDropdown);
            }
            if($serviceDropdown.length){
                // Fetch service list with providerId and categoryId
                fetchServiceList($providerDropdown.val(), categoryId, $subcategoryDropdown.val(), $serviceDropdown);
            }
            }
           
        
        });

        // Handle subcategory change
        $subcategoryDropdown.off('change').on('change', function () {
            const subcategoryId = $(this).val();
            totalServicePrice = 0;
            updateTotalPrice(totalServicePrice);
            if(subcategoryId){
            if($serviceDropdown.length){
                // Fetch service list with providerId, categoryId, and subcategoryId
                fetchServiceList($providerDropdown.val(), $categoryDropdown.val(), $(this).val(), $serviceDropdown);
            }
            }
        
        });
}

   // Initialize all dropdowns
    $('.select2js-provider').each(function () {
        const $providerDropdown = $(this);
        const languageId = $providerDropdown.data('language-id');
        const $categoryDropdown = $(`#category_id_${languageId}`);
        const $subcategoryDropdown = $(`#subcategory_id_${languageId}`);
        const $serviceDropdown = $(`#service_id_${languageId}`);

        if ($categoryDropdown.length || $subcategoryDropdown.length || $serviceDropdown.length) {
            updateDropdowns($providerDropdown, $categoryDropdown, $subcategoryDropdown, $serviceDropdown);
        }

        initializeSelect2($providerDropdown);
        initializeSelect2($categoryDropdown);
        initializeSelect2($subcategoryDropdown);
        initializeSelect2($serviceDropdown);
    });

    $('.select2js-category').each(function () {
        const $categoryDropdown = $(this);
        const languageId = $categoryDropdown.data('language-id');
        const $providerDropdown = $(`#provider_id_${languageId}`);
        const $subcategoryDropdown = $(`#subcategory_id_${languageId}`);
        const $serviceDropdown = $(`#service_id_${languageId}`);

        if ($subcategoryDropdown.length || $serviceDropdown.length) {
            updateDropdowns($providerDropdown, $categoryDropdown, $subcategoryDropdown, $serviceDropdown);
        }

        initializeSelect2($categoryDropdown);
        initializeSelect2($subcategoryDropdown);
        initializeSelect2($serviceDropdown);
    });
    $('.select2js-service').on('select2:select', function (e) {
        const selectedService = e.params.data;

        if (selectedService && selectedService.price) {
            totalServicePrice += parseFloat(selectedService.price);
            updateTotalPrice(totalServicePrice); // Update the total price when a service is selected
        }
    });

    // Event handler for when a service is deselected
    $('.select2js-service').on('select2:unselect', function (e) {
        const deselectedService = e.params.data;
        if (deselectedService && deselectedService.price) {
            totalServicePrice -= parseFloat(deselectedService.price);
            updateTotalPrice(totalServicePrice); // Update the total price when a service is deselected
        }
    });

    $('[data-select2-type]').each(function () {
        initializeSelect2($(this));
    });
    // Listen for changes and synchronize all dropdowns of the same type
    $('[data-select2-type]').on('select2:select', function (e) {
        const $dropdown = $(this);
        const selectedId = e.params.data.id;
        let selectedIds = null;
        // Check if the dropdown is multiple-select
        if ($dropdown.prop('multiple')) {
            selectedIds = Array.from(new Set($dropdown.val())); // Get unique selected values
        }
        const type = $dropdown.data('select2-type');
        synchronizeDropdowns(type, selectedIds,selectedId);
    });
    

    // Handle language toggle
    $('.language-toggle').on('click', function () {
        const languageId = $(this).data('language-id');
       // $('.language-form').hide();
        $(`#form-language-${languageId}`).show();
    });
});


</script>

<script>
    // Text Editor code
      if (typeof(tinyMCE) != "undefined") {
         // tinymceEditor()
         function tinymceEditor(target, button, callback, height = 200) {
            var rtl=$("html[lang=ar]").attr('dir');
            tinymce.init({
               selector: target || '.textarea',
               directionality : rtl,
               height: height,
               skin: 'oxide-dark',
               relative_urls: false,
               remove_script_host: false,
               content_css: [
                  'https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
                  'https://www.tinymce.com/css/codepen.min.css'
               ],
               image_advtab: true,
               menubar: false,
               plugins: ["textcolor colorpicker image imagetools media charmap link print textcolor code codesample table"],
               toolbar: "image undo redo | link image | code table",
               toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist | removeformat | code | image |' + button,
               image_title: true,
               automatic_uploads: true,
               setup: callback,
               convert_urls:false,
               file_picker_types: 'image',
               file_picker_callback: function(cb, value, meta) {
                  var input = document.createElement('input');
                  input.setAttribute('type', 'file');
                  input.setAttribute('accept', 'image/*');

                  input.onchange = function() {
                     var file = this.files[0];

                     var reader = new FileReader();
                     reader.onload = function() {
                        var id = 'blobid' + (new Date()).getTime();
                        var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                        var base64 = reader.result.split(',')[1];
                        var blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);

                        cb(blobInfo.blobUri(), { title: file.name });
                     };
                     reader.readAsDataURL(file);
                  };
                  input.click();
               }
            });
         }
      }
      function showCheckLimitData(id){
         var checkbox =  $('#'+id).is(":checked")
         if(checkbox == true){
            $('.'+id).removeClass('d-none')
         }else{
            $('.'+id).addClass('d-none')

         }
      }

      function validateModal(modalId) {
       var isValid = true;

   $('#' + modalId + ' input[required], #' + modalId + ' textarea[required]').next('small.help-block').text('');

   $('#' + modalId + ' input[required], #' + modalId + ' textarea[required]').each(function() {
      if ($(this).val().trim() === '') {
         $(this).next('small.help-block').text('This field is required');
         isValid = false;
      }
   });
   if (!isValid) {
      return false;
   }
   // If all inputs are valid, hide the modal
   $('#' + modalId).modal('hide');
}

</script>
 @yield('bottom_script')

 <!-- Flextree Javascript-->
 <script src="{{ asset('vendor/magnific-popup/jquery.magnific-popup.min.js') }}" defer></script>
 <script src="{{ asset('js/flex-tree.min.js')}}" defer></script>
 <script src="{{ asset('js/tree.js')}}" defer></script>

 <!-- Table Treeview JavaScript -->
 <!-- <script src="{{ asset('js/table-treeview.js')}}"></script> -->

 <!-- SweetAlert JavaScript -->
 <script src="{{ asset('js/sweetalert.js')}}"></script>

 <!-- Vectoe Map JavaScript -->
 <script src="{{ asset('js/vector-map-custom.js')}}"></script>

 <!-- Chart Custom JavaScript -->
 <script src="{{ asset('js/customizer.js')}}"></script>

 <script src="{{ asset('vendor/confirmJs/confirm.min.js')}}"></script>

 <script src="{{ asset('vendor/vanillajs-datepicker/dist/js/datepicker-full.js')}}"></script>

 <script src="{{ asset('js/charts/progressbar.js')}}"></script>

 <!-- Chart Custom JavaScript -->
 <script src="{{ asset('js/chart-custom.js')}}"></script>
 <script src="{{ asset('js/charts/01.js')}}"></script>
 <script src="{{ asset('js/charts/02.js')}}"></script>

 <!-- slider JavaScript -->
 <!-- <script src="{{ asset('js/slider.js')}}"></script> -->

 <!-- Emoji picker -->
 <script src="{{ asset('vendor/emoji-picker-element/index.js')}}" type="module"></script>
 @if(isset($assets) && (in_array('datatable',$assets) || in_array('datatable_builder',$assets)))
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.select.min.js') }}"></script>
@endif
    <script src="{{ asset('vendor/fullcalendar/core/main.js') }}"></script>
    <script src="{{ asset('vendor/fullcalendar/interaction/main.js') }}"></script>
    <script src="{{ asset('vendor/fullcalendar/daygrid/main.js') }}"></script>
    <script src="{{ asset('vendor/fullcalendar/timegrid/main.js') }}"></script>
    <script src="{{ asset('vendor/fullcalendar/list/main.js') }}"></script>
    <script src="{{ asset('vendor/fullcalendar/bootstrap/main.js') }}"></script>
 <!-- app JavaScript -->
   <script src="{{ asset('js/app.js')}}"></script>
 @include('helper.app_message')
