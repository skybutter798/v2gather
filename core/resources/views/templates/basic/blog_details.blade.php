@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-details-section blog-section bg--gray ptb-80">
        <div class="container">
            <div class="row justify-content-center mb-30-none">
                <div class="col-xl-8 mb-30">
                    <div class="blog-item">
                        <div class="blog-thumb">
                            <img src="{{ getImage('assets/images/frontend/blog/' . $blog->data_values->blog_image, '770x520') }}" alt="@lang('blog')">
                            <span class="blog-date text-center">{{ showDateTime($blog->created_at, $format = 'd M') }}</span>
                        </div>
                        <div class="blog-content">
                            <h3 class="blog-title">{{ __($blog->data_values->title) }}</h3>
                            <hr>

                            <p>@php echo $blog->data_values->description; @endphp</p>
                            <ul class="share-link">
                                <strong class="me-3 mt-1">@lang('Share'):</strong>
                                <li>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}">
                                        <span class="lab la-facebook-f"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://twitter.com/intent/tweet?text={{ __($blog->data_values->title) }}&amp;url={{ urlencode(url()->current()) }}">
                                        <span class="lab la-twitter"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ __($blog->data_values->title) }}&amp;summary=dit is de linkedin summary">
                                        <span class="fab fa-linkedin"></span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </div>
                    <div class="comments-area mt-2">
                        <div class="fb-comments" data-width="100%" data-numposts="5"></div>
                    </div>
                </div>

                <div class="col-xl-4 mb-30">
                    <div class="sidebar">
                        <div class="widget-box">
                            <h5 class="widget-title">@lang('Latest Blog')</h5>
                            <div class="popular-widget-box">
                                @foreach ($latestBlogs as $latestBlog)
                                    <div class="single-popular-item d-flex flex-wrap align-items-center">
                                        <div class="popular-item-thumb">
                                            <img src="{{ getImage('assets/images/frontend/blog/' . @$latestBlog->data_values->blog_image) }}">
                                        </div>
                                        <div class="popular-item-content">
                                            <a href="{{ route('blog.details', [$latestBlog->id, slug(@$latestBlog->data_values->title)]) }}">
                                                <h6 class="title">
                                                    {{ __(strLimit(strip_tags(@$latestBlog->data_values->title), 100)) }}
                                                </h6>
                                            </a>
                                            <span class="blog-date">{{ showDateTime(@$latestBlog->created_at, $format = 'd F, Y') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('fbComment')
    @php echo loadExtension('fb-comment') @endphp
@endpush
