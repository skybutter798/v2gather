@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-section bg--gray ptb-80">
        <div class="container">
            <div class="row justify-content-center mb-30-none">
                @foreach ($blogs as $blog)
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 mb-30">
                        <div class="blog-item">
                            <div class="blog-thumb">
                                <img src="{{ getImage('assets/images/frontend/blog/thumb_' . @$blog->data_values->blog_image, '370x215') }}" alt="@lang('blog')">
                                <span class="blog-date text-center">{{ showDateTime($blog->created_at, 'd M') }}</span>
                            </div>
                            <div class="blog-content">
                                <h3 class="title">
                                    <a href="{{ route('blog.details', [$blog->id, slug(@$blog->data_values->title)]) }}">
                                        {{ __(@$blog->data_values->title) }}
                                    </a>
                                </h3>
                                <p> {{ __(strLimit(strip_tags(@$blog->data_values->description), 80)) }}
                                    <span class="fw-bold readmore text--base">
                                        <a href="{{ route('blog.details', [$blog->id, slug(@$blog->data_values->title)]) }}">@lang('Read more')
                                            <i class="fas fa-arrow-right"></i></a>
                                    </span>

                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($blogs->hasPages())
                <div class="row justify-content-center mb-30-none">
                    <div class="col-lg-12 mb-30">
                        {{ paginateLinks($blogs) }}
                    </div>
                </div>
            @endif
        </div>
    </section>

    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
