@component('mail::message')
# Introduction

The body of your message.

<a href="{{route('home')}}">
    Hello there
</a>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
