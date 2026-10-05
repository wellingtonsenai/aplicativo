function buscarUsuario(usuarios, matricula) {
  const encontrados = usuarios.filter((usuario) => usuario.matricula === matricula);

  if (encontrados.length > 0) {
    return encontrados[0];
  }
  return null;
}

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

function autenticar(usuarios, matricula, senha) {
  const usuario = buscarUsuario(usuarios, matricula);

  if (usuario === null || usuario.senha !== senha) {
    return { aceito: false, mensagem: "Matrícula ou senha incorreta.", usuario: null };
  }
  return { aceito: true, mensagem: `Bem-vindo(a), ${usuario.nome}!`, usuario: usuario };
}
