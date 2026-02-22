@extends('landing-page.layouts.default')
 

@section('content')
<div class="section-padding booking">
    <div class="container">
        
        @if($data->isEmpty())
            <helpdesk-page :employee_id="{{ auth()->user()->id }}" :canhelpdesklist ='@json($addpermission)'></helpdesk-page>
        @else
            <helpdesk-table link="{{ route('helpdesk.data') }}" :employee_id="{{ auth()->user()->id }}" :canhelpdesklist ='@json($addpermission)'></helpdesk-table>
        @endif
    </div>
</div>
@endsection