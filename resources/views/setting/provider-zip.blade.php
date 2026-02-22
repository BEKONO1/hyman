<div class="col-md-12">
    <form action="{{ route('upload.zip') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label class="form-control-label" for="file">Upload ZIP File:</label>
            <input type="file" name="file" id="file" class="form-control" required>
        </div>
        <div class="d-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
</div>

<div class="mt-4">
    <div class="col-lg-12">
        <div>
            <strong>Note:</strong>
            <ul class="mb-0 mt-2">
                <li>
                    <strong>Commission:</strong> If you choose "Commission," the system will charge providers a percentage or a flat fee on each booking or transaction. <br>
                    &emsp;<i>Logic:</i> This means that every time a customer books a service, the platform takes a cut (based on the chosen commission percentage or flat fee). 
                    For example, if the commission is set at 20%, and a handyman completes a job for $100, the platform will take $20, and the provider will receive $80.
                </li>
                <br>
                <li>
                    <strong>Subscription:</strong> If you choose "Subscription," providers will pay a fixed fee periodically (e.g., monthly or yearly) to use the platform's services. <br>
                    &emsp;<i>Logic:</i> This model allows service providers to pay a fixed amount every month or year to stay active on the platform. 
                    For example, if a provider pays $50 per month as a subscription, regardless of how many jobs they complete, they will continue to have access to the platform.
                </li>
            </ul>
        </div>
    </div>
</div>