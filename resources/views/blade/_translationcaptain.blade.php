@if (Cookie::get('translationcaptain'))
    <h1>                                  TRANSLATIONCAPTAIN LOADED</h1>
    <script src="https://backend.bedrock.local/js/tc/index.js"></script>
    <script>
    // project

        function getCookieKey()
        {
            return "translationcaptain_context";
        }

        function getCurrentLanguage()
        {
            return 'en';
        }

    </script>
@endif
