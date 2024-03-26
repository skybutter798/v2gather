@php
    $footer = getContent('footer.content', true);
    $socials = getContent('social_icon.element', orderById: true);
@endphp
<footer class="footer-section pt-80 bg-overlay-black bg_img"
    data-background="{{ getImage('assets/images/frontend/footer/' . @$footer->data_values->background_image, '1920x390') }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 text-center">
                <div class="footer-social-area">
                    <ul class="footer-social">
                        @foreach ($socials as $social)
                            <li>
                                <a href="{{ @$social->data_values->url }}" target="_blank"
                                    title="{{ @$social->data_values->title }}">
                                    @php echo @$social->data_values->icon; @endphp
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom-area">
            <div class="row justify-content-center">
                <div class="col-lg-12 text-center">
                    <p>@lang('Copyright') &copy; {{ date('Y') }}. @lang('All Rights Reserved')</p>
                </div>
            </div>
        </div>
    </div>
</footer>
