fetch("http://localhost/api/v1/auth/login", {
    method: "POST",
    headers: {
        "Content-Type": "application/json"
    },
    body: JSON.stringify({
        email: "admin@gmail.com",
        password: "aetherphp",
        csrf_token: "<?= \Aether\Security\Token\CsrfToken::_get(); ?>"
    })
}).then(response => {
    if (!response.ok)
        throw new Error("Erreur HTTP : " + response.status);

    console.log(response.text());
})
