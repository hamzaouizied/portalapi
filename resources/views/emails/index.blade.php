@component('mail::message')
    <p class="head-mail">Welcome {{ $name }},</p>
    <p class="text-mail">You can access your account by clicking by using the link <a href="{{$link}}">{{$link}}</a></p>
@endcomponent