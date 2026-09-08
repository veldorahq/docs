@extends('layouts.app')

@section('page_title', 'MIT License')
@section('page_title_suffix', 'Veldora Framework')
@section('meta_desc', 'Open source MIT license for the Veldora PHP Framework.')

@section('content')
<main class="page-container static-prose-page">

    <div class="static-prose-header">
        <h1 class="static-page-title" style="font-size:2.2rem;">MIT License</h1>
        <p class="static-prose-meta">Copyright &copy; 2026 Shahriyar Fahim &amp; Veldora Contributors</p>
    </div>

    <div class="license-text-block">
        <p>
            Permission is hereby granted, free of charge, to any person obtaining a copy
            of this software and associated documentation files (the "Software"), to deal
            in the Software without restriction, including without limitation the rights
            to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
            copies of the Software, and to permit persons to whom the Software is
            furnished to do so, subject to the following conditions:
        </p>

        <p>
            The above copyright notice and this permission notice shall be included in all
            copies or substantial portions of the Software.
        </p>

        <p class="license-disclaimer">
            THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
            IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
            FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
            AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
            LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
            OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
            SOFTWARE.
        </p>
    </div>

    <div class="static-back-nav">
        <a href="/terms" class="btn btn-ghost btn-sm">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
            Terms of Service
        </a>
        <a href="https://github.com/veldorahq/veldora-core/blob/main/LICENSE" target="_blank" rel="noopener noreferrer" class="btn btn-ghost btn-sm">
            View on GitHub
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        </a>
    </div>

</main>
@endsection
