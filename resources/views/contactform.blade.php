<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soumission de formulaire de contact</title>
</head>
<body>
    <h1>Nouvelle soumission de formulaire de contact</h1>
    <p><strong>Nom:</strong> {{ $details['name'] }}</p>
    <p><strong>Téléphone:</strong> {{ $details['phone'] }}</p>
    <p><strong>Email:</strong> {{ $details['email']  }}</p>
    <p><strong>Sujet:</strong> {{ $details['subject']  }}</p>
    <p><strong>Message:</strong> {{ $details['message'] }}</p>
</body>
</html>
