
function mostrarMensagem(texto, tipo) {
  const mensagem = document.getElementById("mensagem");
  mensagem.textContent = texto;
  mensagem.className = "mensagem " + tipo;
}

async function entrar() {
  const matricula = document.getElementById("matricula").value.trim();
  const senha = document.getElementById("senha").value;

  const erro = validarCampos(matricula, senha);
  if (erro !== "") {
    mostrarMensagem(erro, "erro");
    return;
  }

  const resultado = await autenticar(matricula, senha);

  console.log(resultado);
  if (resultado.aceito) {
    // localStorage('matricula', resultado.usuario.matricula);
    // localStorage('nome', resultado.usuario.nome);
    // localStorage('perfil', resultado.usuario.perfil);
    mostrarMensagem(resultado.mensagem + " Perfil: " + resultado.usuario.perfil + ".", "sucesso");
    location.href = "lanche.html";
  } else {
    mostrarMensagem(resultado.mensagem, "erro");
  }
}

function montarMenu() {
  const links = [["lanche.html", "Pegar lanche"]];
  if (localStorage.getItem("perfil") === "supervisor") {
    links.push(["entregues.html", "Entregues"], ["alunos.html", "Alunos"], ["turmas.html", "Turmas"], ["usuarios.html", "Usuários"]);
  }
  links.push(["usuario.html", "Meu perfil"]);

  let html = "";
  links.forEach(([pagina, texto]) => {
    const atual = location.pathname.endsWith(pagina) ? ' class="atual"' : "";
    html += '<a href="' + pagina + '"' + atual + ">" + texto + "</a>";
  });
  html += '<a href="#" onclick="sair(); return false;">Sair</a>';
  document.getElementById("menu").innerHTML = html;
}

async function sair() {
  await fetch("api/logout.php");
  localStorage.clear();
  location.href = "index.html";
}

// Só as telas internas têm <nav id="menu">; o login não
if (document.getElementById("menu")) {
  montarMenu();
}
