
# Page Flipper - Documentação

![GitHub release](https://img.shields.io/github/v/release/willderazevedo/page-flipper)  ![License](https://img.shields.io/github/license/willderazevedo/page-flipper)

## Descrição
O **Page Flipper** é um plugin **gratuito** para WordPress que permite a criação de livros digitais interativos. Ele adiciona um novo post type para livros digitais, oferecendo um conjunto de funcionalidades para gerenciar livros e adicionar interatividade com hotspots.

## Funcionalidades

- **Post Type Personalizado:**
  - Um novo tipo de post para livros digitais.
  - Categorias exclusivas para os livros digitais.
- **Construtor de Livros:**
  - Upload de imagens para as páginas do livro.
  - Reordenação de páginas.
  - Adição e remoção de páginas.
  - Adição de **hotspots** interativos, como:
    - Narração
    - Áudio
    - Vídeo
    - Imagem
    - Texto
    - Link
- **Upload de PDF:**
  - Opcional, para permitir o download do livro em PDF.
- **Shortcode para Incorporação:**
  - Incorporar o livro digital em qualquer parte do site.
  - Formato padrão: `[page_flipper id="post_id"]`
  - Parâmetros opcionais:

| Parâmetro      | Descrição                                         | Valores Possíveis | Padrão    |
|---------------|-------------------------------------------------|-----------------|-----------|
| `summary`     | Exibe ou oculta o sumário                      | `yes` ou `no`  | `yes`     |
| `action_bar`  | Exibe ou oculta a barra de ações               | `yes` ou `no`  | `yes`     |
| `controls`    | Exibe ou oculta os controles                   | `yes` ou `no`  | `yes`     |
| `page_bg`     | Cor de fundo das páginas                       | Hexadecimal    | `#333333` |
| `action_bar_bg` | Cor de fundo da barra de ações               | Hexadecimal    | `#555555` |
| `summary_bg`  | Cor de fundo do sumário                        | Hexadecimal    | `#555555` |
| `controls_icon` | Cor dos ícones dos controles                 | Hexadecimal    | `#ffffff` |
| `font_color`  | Cor da fonte do livro                          | Hexadecimal    | `#ffffff` |

- **Integração com Elementor:**
  - Widget para adicionar livros digitais.
  - Suporte para selecionar um livro específico ou utilizar a query atual.

## Suporte a Idiomas
O plugin está disponível nos seguintes idiomas:

- Inglês
- Português Brasileiro
- Francês
- Russo
- Espanhol
- Japonês
- Chinês Tradicional
- Chinês Simplificado

## Contribuição
Se você deseja contribuir para o desenvolvimento do **Page Flipper**, acesse o repositório no GitHub:

[Repositório no GitHub](https://github.com/willderazevedo/page-flipper)