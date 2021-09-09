<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
</head>
<body>
<div>
    <table>
        <tr>
            <td>
                <button onclick="redirectToMeli()">Authorize App</button>
            </td>
        </tr>
    </table>
</div>

<script>
    let submitting = false, authUrl = null, code = null, token = null, me = null, test_user = null,orders = null, applications = null;

    function mounted() {
        if (checkIfCode()) {
            // get token by code
            fetchToken().then(data => {
                token = data;
                console.log('token', token)
            });
        }
    }
    mounted();
    function checkIfCode() {
        let params = (new URL(document.location)).searchParams;
        if (params.has("code")) {
            console.log("params.has(code)")
            code = params.get("code");
            console.log("code", code)
            return true;
        } else {
            console.log("params.haveNot(code)")
            return false;
        }
    }
    async function fetchToken() {
        try {
            let url = "{{route('meli.getToken')}}";
            console.log("url", url)
            const response = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({code: code})
            });
            return response.json();
        } catch(error) {
            console.error(error);
        }
    }
    async function fetchAuthorizationUrl() {
        try {
            let url = "{{route('meli.getAuthorizationUrl')}}";
            const response = await fetch(url);
            return response.json();
        } catch(error) {
            console.error(error);
        }
    }
    function redirectToMeli() {
        // get url
        fetchAuthorizationUrl()
            .then(data => {
                // redirect
                if (data) window.location.replace(data.data.url);
            });
    }
</script>
</body>
</html>


