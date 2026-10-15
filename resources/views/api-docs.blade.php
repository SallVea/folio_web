@extends('layouts.public')
@section('title','API Docs')
@section('content')
<div class="max-w-6xl mx-auto py-10">
    <h1 class="wordmark mb-6">API Docs</h1>
    <div id="swagger-ui"></div>
</div>
<link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css" />
<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
<script>
window.onload = () => {
    SwaggerUIBundle({ url: '/openapi.json', dom_id: '#swagger-ui' });
};
</script>
@endsection