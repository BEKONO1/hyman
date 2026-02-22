@extends('landing-page.layouts.default')


@section('content')
<div class="my-5">
   <h4 class="text-center text-capitalize fw-bold my-5">{{__('landingpage.data_deletion_request')}}</h4>
   <div class="container">
      {!! $deletion_content!!}
   </div>
 </div>

 <script>
   tinymce.init({
      selector: '.container',
      height: 500,
      plugins: 'code',
      toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | code',
   });
</script>
@endsection
