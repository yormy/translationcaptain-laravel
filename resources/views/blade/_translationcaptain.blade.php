@if (config('translationcaptain-laravel.enabled') && Cookie::get('translationcaptain'))
    <h1>                                  TRANSLATIONCAPTAIN LOADED</h1>
    <script>
    const projectId='{{config('translationcaptain-laravel.projectId')}}';

        function getCookieKey()
        {
            return "translationcaptain_context";
        }

        function getCurrentLanguage()
        {
            return 'en';
        }

        function getPostContextUrl()
        {
            return '{{config('translationcaptain-laravel.url')}}';
        }

    </script>
    <script  type="text/javascript" src="https://backend.bedrock.local/js/tc/index.js">
    </script>
@endif
