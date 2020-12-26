@if (config('translationcaptain.enabled') && Cookie::get('translationcaptain'))
    <h1>                                  TRANSLATIONCAPTAIN LOADED</h1>
    <script>
        const projectId='{{config('translationcaptain.project_id')}}';

        function getCurrentLanguage()
        {
            return 'en';
        }


        function getCookieKey()
        {
            return "translationcaptain_context";
        }



        function getPostContextUrl()
        {
            return '{{config('translationcaptain.url')}}';
        }

    </script>
    <script  type="text/javascript" src="https://backend.bedrock.local/js/tc/index.js">
    </script>
@endif
