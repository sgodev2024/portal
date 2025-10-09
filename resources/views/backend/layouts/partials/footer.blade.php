<footer class="footer">
    <div class="container-fluid d-flex justify-content-between">
        <nav class="pull-left">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="3">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"> </a>
                </li>
            </ul>
        </nav>
        <div class="copyright">
            @if (!empty($company?->footer))
                {!! $company->footer !!}
            @else
                © {{ date('Y') }}, made with
                <i class="fa fa-heart text-danger"></i> by
                <a href="https://sgomedia.vn" target="_blank">SGO Việt Nam</a>
            @endif
        </div>
        <div>
            <a target="_blank" href="#"></a>
        </div>
    </div>
</footer>
