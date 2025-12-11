</div> <!-- container-fluid -->
</div><!-- End Page-content -->

@include('admin.partials.footer_bottom_bar')

</div>
<!-- end main content-->

</div>
<!-- END layout-wrapper -->



<!--start back-to-top-->
<button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
    <i class="ri-arrow-up-line"></i>
</button>
<!--end back-to-top-->

<!--preloader-->
<div id="preloader">
    <div id="status">
        <div class="spinner-border text-primary avatar-sm" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>
<script src="https://cdn.lordicon.com/lordicon.js"></script>

@include('admin.partials.modal')
@include('admin.partials.appjs')
@include('admin.partials.canvas')
@include('admin.partials.toast')
@include('admin.partials.footer_includes')
@include('admin.partials.footer_scripts')

</body>

</html>