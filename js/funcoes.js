function validarCampos(matricula, senha) {
  if (matricula === "" && senha === "") {
    return "Informe a matrícula e a senha.";
  }
  if (matricula === "") {
    return "Informe a matrícula.";
  }
  if (senha === "") {
    return "Informe a senha.";
  }
  return "";
}

async function autenticar(matricula, senha) {
  const resposta = await fetch('api/login.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ matricula: matricula, senha: senha })
  });
  const resultado = await resposta.json();

  if (resultado.aceito) {
    localStorage.setItem('nome', resultado.usuario.nome);
    localStorage.setItem('matricula', resultado.usuario.matricula);
    localStorage.setItem('perfil', resultado.usuario.perfil);
  }
  return resultado;
}
