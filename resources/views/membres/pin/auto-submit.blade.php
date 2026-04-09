<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation en cours...</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            color: #333;
        }
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #142850; /* primary-dark-blue */
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h2 { font-weight: 300; }
        p { color: #6c757d; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="loader"></div>
    <h2>Vérification réussie</h2>
    <p>Redirection en cours vers votre opération, veuillez patienter...</p>

    {{-- Formulaire invisible recréant exactement la requête interceptée --}}
    <form id="autoSubmitForm" action="{{ $action_url }}" method="POST" style="display: none;">
        @csrf
        @if(strtoupper($action_method) !== 'POST')
            <input type="hidden" name="_method" value="{{ $action_method }}">
        @endif
        
        @foreach($action_data as $key => $value)
            @if(is_array($value))
                {{-- Gestion basique des tableaux unidimensionnels (ex: tags[]) --}}
                @foreach($value as $v)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
    </form>

    <script>
        // Auto-soumission dès que la page est chargée
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                document.getElementById('autoSubmitForm').submit();
            }, 500); // Léger délai visuel rassurant
        });
    </script>
</body>
</html>
