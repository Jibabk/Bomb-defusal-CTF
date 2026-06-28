# Bomb Defusal CTF

Bomb Defusal CTF é um desafio do gênero Capture The Flag em que o jogador precisa desarmar uma bomba antes que o temporizador acabe. A bomba possui oito fios, e cada fio representa um desafio diferente de criptoanálise.

## Autores

* EDUARDO MARCIANO DE MELO MENESES - 211055227
* JEAN BUENO KARIA - 211055290

## Requisitos

* Docker Engine instalado
* Docker Compose plugin instalado
* Portas locais 21, 20, 8000, 2222 e 21100-21110 livres

## Como Subir a Aplicação

Na raiz do projeto, execute:

```bash
docker compose up --build
```

Para parar o ambiente:

```bash
docker compose down
```

Para reiniciar o desafio do zero, removendo os volumes persistidos:

```bash
docker compose down -v
docker compose up --build
```

## Estrutura do Projeto

```text
.
├── Dockerfile
├── docker-compose.yml
├── ftp_data/
├── secret/
├── src/
│   ├── Controllers/
│   ├── Core/
│   ├── Jobs/
│   ├── Models/
│   ├── Utils/
│   ├── Views/
│   └── public/
```

## Walkthrough

O passo a passo para a resolução do desafio se encontra em:

```text
WALKTHROUGH.md
```

## Aviso de Segurança

Este ambiente foi criado para fins educacionais e contém vulnerabilidades intencionais. Não exponha os containers para a internet e não execute o projeto fora de um ambiente isolado, pois isso pode causar danos ao seu sistema ou aos seus arquivos. O uso recomendado é exclusivamente local, em rede privada ou em uma máquina virtual dedicada.
