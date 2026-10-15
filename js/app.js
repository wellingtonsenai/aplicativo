
function mostrarMensagem(texto, tipo) {
  const mensagem = document.getElementById("mensagem");
  mensagem.textContent = texto;
  mensagem.className = "mensagem " + tipo;
}

function entrar() {
  const matricula = document.getElementById("matricula").value.trim();
  const senha = document.getElementById("senha").value;

  const erro = validarCampos(matricula, senha);
  if (erro !== "") {
    mostrarMensagem(erro, "erro");
    return;
  }

  const resultado = autenticar(usuarios, matricula, senha);

  console.log(resultado);
  if (resultado.aceito) {
    // localStorage('matricula', resultado.usuario.matricula);
    // localStorage('nome', resultado.usuario.nome);
    // localStorage('perfil', resultado.usuario.perfil);
    mostrarMensagem(resultado.mensagem + " Perfil: " + resultado.usuario.perfil + ".", "sucesso");
  } else {
    mostrarMensagem(resultado.mensagem, "erro");
  }
}
