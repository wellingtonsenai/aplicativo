let usuarios = [];

fetch("dados/usuarios.json")
  .then((resposta) => resposta.json())
  .then((lista) => {
    usuarios = lista;
    console.log(usuarios);
  })
  .catch(() => {
    mostrarMensagem("Não foi possível carregar os usuários. Abra o projeto pelo Live Server.", "erro");
  });

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

  if (resultado.aceito) {
    mostrarMensagem(resultado.mensagem + " Perfil: " + resultado.usuario.perfil + ".", "sucesso");
  } else {
    mostrarMensagem(resultado.mensagem, "erro");
  }
}
