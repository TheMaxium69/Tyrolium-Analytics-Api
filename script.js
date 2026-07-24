(async () => {

    // j'ai trouver un truc comme ca mais je suis pas sur je sait meme pas comment le tester

    // const project = new URL(document.currentScript.src).searchParams.get("project");

    fetch('http://localhost:8000/input', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            // project: project
            project: 'TyroTag-d531274a380d0aea1b23aa256629cc896a5f50a7e0fa6',
            ip: (await (await fetch('https://api.ipify.org?format=json')).json()).ip,
            page_name: document.title,
            uri: location.href,
            is_login: false
        })
    });
})();
