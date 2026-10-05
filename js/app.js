let usuarios = [
  { "matricula": "1001", "nome": "Ana Souza", "senha": "cantina123", "perfil": "atendente", "ativo": true },
  { "matricula": "1002", "nome": "Bruno Lima", "senha": "lanche2026", "perfil": "atendente", "ativo": true },
  { "matricula": "2001", "nome": "Carla Mendes", "senha": "super2026", "perfil": "supervisor", "ativo": true },
  { "matricula": "2002", "nome": "Diego Rocha", "senha": "diego2026", "perfil": "atendente", "ativo": false }
];


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
    const usuarioLogado = {
      matricula: resultado.usuario.matricula,
      nome: resultado.usuario.nome,
      perfil: resultado.usuario.perfil
    };
    localStorage.setItem("usuarioLogado", JSON.stringify(usuarioLogado));
    console.log(localStorage.getItem("usuarioLogado"));
    console.log(JSON.parse(localStorage.getItem("usuarioLogado")));
    mostrarMensagem(resultado.mensagem + " Perfil: " + resultado.usuario.perfil + ".", "sucesso");
  } else {
    mostrarMensagem(resultado.mensagem, "erro");
  }
}
