# Requirements Document

## Introduction

Local Commerce é uma plataforma White Label de e-commerce local que permite a qualquer comércio criar e gerenciar sua própria loja online. A plataforma é construída com Next.js 15, React, TypeScript, TailwindCSS, Shadcn UI e Framer Motion. Todos os dados são servidos por arquivos JSON através de uma camada de serviços que simula chamadas REST, garantindo que a migração futura para uma API REST seja feita sem alterações nos componentes. A aplicação não utiliza banco de dados, autenticação, Firebase ou Supabase.

---

## Glossary

- **Platform**: A aplicação Local Commerce como um todo.
- **Store**: Um comércio cadastrado na plataforma, com sua própria página, produtos e configurações.
- **Product**: Item disponível para compra em uma Store.
- **Cart**: Carrinho de compras que acumula Products selecionados pelo Customer.
- **Order**: Pedido finalizado a partir do Cart, contendo os dados de entrega e pagamento.
- **Customer**: Usuário que navega pela plataforma, acessa Stores e realiza pedidos.
- **Admin**: Painel administrativo visual de uma Store, sem autenticação.
- **Service**: Módulo TypeScript que abstrai o acesso aos arquivos JSON, expondo funções assíncronas simulando chamadas REST.
- **Category**: Agrupamento temático de Products ou Stores.
- **Coupon**: Código promocional com desconto aplicável no Cart.
- **Deliverer**: Entregador associado a pedidos de uma Store.
- **JSON_Data**: Arquivos JSON localizados em `data/` que simulam respostas de API.
- **Skeleton**: Componente visual de carregamento que imita a forma do conteúdo real.
- **Toast**: Notificação temporária exibida na interface.
- **Drawer**: Painel lateral deslizante, usado para o Cart.
- **Modal**: Janela sobreposta para confirmações e detalhes.
- **Theme**: Configuração de tema visual (claro ou escuro) da Platform.

---

## Requirements

### Requirement 1: Arquitetura de Dados e Serviços

**User Story:** Como desenvolvedor, quero que todos os dados da aplicação sejam acessados exclusivamente por Services, para que a futura migração para uma API REST não exija alterações nos componentes.

#### Acceptance Criteria

1. THE Platform SHALL organizar os dados em arquivos JSON_Data (`stores.json`, `products.json`, `orders.json`, `categories.json`, `users.json`, `coupons.json`) localizados no diretório `data/`.
2. THE Service SHALL expor funções assíncronas para cada entidade (Stores, Products, Orders, Categories, Coupons, Users) que retornem `Promise` cujo tipo resolvido seja a interface TypeScript correspondente definida no diretório `types/`.
3. THE Platform SHALL enforce that no component in `components/`, `app/`, `hooks/`, or `contexts/` imports from the `data/` directory directly — all data access MUST go through `services/`; a build-time lint rule or TypeScript path restriction SHALL flag any direct import as an error.
4. WHEN a Service function is called, THE Service SHALL return data that conforms to the corresponding interface defined in `types/`, such that assigning the resolved value to a variable typed with that interface produces no TypeScript compile error.
5. THE Platform SHALL organizar o código nos diretórios: `components/`, `hooks/`, `services/`, `types/`, `data/`, `utils/`, `contexts/`, `app/` (pages via Next.js App Router).
6. WHEN a Service call fails (e.g., JSON parse error or missing file), THE Service SHALL reject the returned Promise with an Error instance containing a descriptive message, and the calling component SHALL display a user-visible error state instead of an unhandled exception.

---

### Requirement 2: Identidade Visual e Design System

**User Story:** Como operador da plataforma, quero uma identidade visual profissional e consistente, para que a plataforma pareça um SaaS pronto para ser comercializado.

#### Acceptance Criteria

1. THE Platform SHALL include an SVG logo file where the SVG contains at least one `<path>` or `<text>` element and renders without visible distortion at 32×32 px and 200×200 px viewports.
2. THE Platform SHALL define a color palette in `tailwind.config` with at least: one primary brand color, one accent color, one light background color, and one dark background color — each expressed as a CSS custom property or Tailwind token so they can be referenced by name throughout the codebase.
3. THE Platform SHALL include a `favicon.ico` or `favicon.svg` file in the `public/` directory that visually derives from the SVG logo (same shape or symbol).
4. WHEN the Customer toggles the Theme, THE Platform SHALL apply or remove the Tailwind `dark` class on the `<html>` element within 100ms, causing all `dark:` variant utilities across all visible components to update without a full page reload.
5. WHEN a Store or Product has no image URL in JSON_Data, THE Platform SHALL render a placeholder element with a defined background color from the design palette and centered text or icon — the placeholder SHALL NOT be a broken `<img>` tag.
6. THE Platform SHALL use a single sans-serif font family for body text and a single font family (same or distinct) for headings across all pages; font sizes SHALL follow a defined type scale (e.g., Tailwind's default `text-sm` through `text-4xl`) with no ad-hoc `style` overrides.
7. WHEN a page component mounts, THE Platform SHALL animate it in using a Framer Motion entrance animation with duration between 150ms and 400ms.
8. WHEN the user hovers over an interactive card (ProductCard, StoreCard), THE Platform SHALL trigger a Framer Motion hover animation (scale, shadow, or opacity change) with duration between 150ms and 300ms.
9. WHEN the user navigates between routes, THE Platform SHALL apply a Framer Motion page-transition animation with duration between 200ms and 400ms.

---

### Requirement 3: Página Inicial (Home)

**User Story:** Como Customer, quero uma página inicial rica e informativa, para que eu possa descobrir lojas e produtos disponíveis na plataforma.

#### Acceptance Criteria

1. THE Platform SHALL exibir na Home um banner principal com chamada para ação e imagem de destaque.
2. WHEN the Customer types at least 1 character in the global search field, THE Platform SHALL display filtered results for both Stores and Products matching the query by name.
3. WHEN the Customer types in the global search field, THE Platform SHALL update the displayed results at most 300ms after the last keystroke (debounce of 300ms); if no matches exist, the search results area SHALL display an empty-results message.
4. THE Platform SHALL display search results limited to a maximum of 5 Store results and 5 Product results at a time.
5. THE Platform SHALL exibir na Home uma seção de Categories contendo no mínimo 4 categorias, cada uma com ícone e rótulo clicável; ao clicar, o Customer é redirecionado para `/categorias/{category-slug}`.
6. THE Platform SHALL exibir na Home uma seção de Products em destaque com no mínimo 6 cards de Product.
7. THE Platform SHALL exibir na Home uma seção de Stores parceiras com no mínimo 4 cards de Store.
8. THE Platform SHALL exibir na Home uma seção de benefícios da plataforma contendo no mínimo 3 itens de benefício (ex.: entrega rápida, variedade, segurança), cada um com ícone e descrição textual.
9. THE Platform SHALL exibir na Home um rodapé contendo: links de navegação institucional, ícones de redes sociais com links, e informações de contato (e-mail ou telefone).
10. WHEN um card de Store é clicado, THE Platform SHALL redirecionar o Customer para `/loja/{store-slug}`.
11. WHEN um card de Product na Home é clicado, THE Platform SHALL redirecionar o Customer para a página da Store correspondente ao Product.

---

### Requirement 4: Página da Loja

**User Story:** Como Customer, quero acessar a página individual de uma Store, para que eu possa visualizar seus produtos e iniciar uma compra.

#### Acceptance Criteria

1. THE Platform SHALL create a dynamic route at `/loja/[slug]` that renders a unique page for each Store entry present in JSON_Data, using the Store's `slug` field as the URL segment.
2. WHEN the Customer accesses `/loja/[slug]` for an existing Store, THE Platform SHALL display all of the following Store fields: banner image, logo, name, category, phone number, WhatsApp link, Instagram link, street address, opening hours, delivery fee (formatted as currency), average delivery time (in minutes), and minimum order value (formatted as currency).
3. WHEN the Store's `isOpen` field in JSON_Data is `true`, THE Platform SHALL display a visually distinct "Aberto" badge; WHEN `isOpen` is `false`, THE Platform SHALL display a visually distinct "Fechado" badge.
4. THE Platform SHALL display a horizontal category navigation bar on the Store page listing each Category that has at least one available Product in that Store; clicking a Category label SHALL scroll the page to the corresponding Product section via anchor navigation.
5. WHEN the Customer types at least 1 character in the Store's search field, THE Platform SHALL display only the Products of that Store whose name or description contains the query string (case-insensitive), updating results within 300ms of the last keystroke.
6. WHEN a Category filter is selected in the Store page, THE Platform SHALL display only the Products belonging to that Category, hiding all others without a page reload.
7. THE Platform SHALL display each Product in a card containing: image (or placeholder), name, truncated description (max 100 characters), base price formatted as BRL currency, promotional price when present, and an "Adicionar" button.
8. IF the Store slug does not match any entry in JSON_Data, THEN THE Platform SHALL render a custom 404 page containing a descriptive error message and a link that navigates back to the Home page (`/`).

---

### Requirement 5: Produtos

**User Story:** Como Customer, quero visualizar informações detalhadas de cada Product, para que eu possa tomar uma decisão de compra informada.

#### Acceptance Criteria

1. THE Platform SHALL display in each ProductCard: an image (or placeholder when absent), the product name, a short description of at most 150 characters, the base price formatted as BRL currency, and — when a promotional price exists — the promotional price formatted as BRL currency.
2. WHEN a Product has a promotional price, THE Platform SHALL render the original price with a CSS `line-through` decoration in a muted color and display the promotional price in a visually distinct color (e.g., brand primary or red), positioned adjacent to the original price.
3. WHEN a Product's `available` field is `false`, THE Platform SHALL render the "Adicionar" button in a disabled state (non-interactive, visually dimmed) and display an "Indisponível" label on or near the card.
4. WHEN a Product has one or more tags defined in JSON_Data, THE Platform SHALL render each tag as a pill-style label beneath the product description.
5. WHEN the Customer clicks the "Adicionar" button on an available Product, THE Platform SHALL add the Product to the Cart and display a Toast notification confirming the addition; the Toast SHALL remain visible for at least 3 seconds before auto-dismissing.

---

### Requirement 6: Carrinho de Compras (Cart)

**User Story:** Como Customer, quero gerenciar meu Cart antes de finalizar a compra, para que eu possa revisar os itens, quantidades e o total.

#### Acceptance Criteria

1. WHEN the Customer opens the Cart, THE Platform SHALL render it as a Drawer sliding in from the right side of the viewport, overlaying the current page content, accessible from any Store page.
2. WHEN a Product is added to the Cart, THE Platform SHALL update the Cart icon badge in the Navbar to reflect the new total unit count within 300ms; the badge count represents the sum of all item quantities in the Cart.
3. WHEN the Customer interacts with the quantity controls for a Cart item, THE Platform SHALL allow increasing the quantity up to a maximum of 99 units per Product and decreasing it down to 0.
4. WHEN the quantity of a Cart item is decremented to 0, THE Platform SHALL remove that item from the Cart automatically without requiring an additional confirmation.
5. WHEN the Cart contains at least one item, THE Platform SHALL display: the subtotal (sum of each item's unit price × quantity) formatted as BRL currency, the Store's delivery fee formatted as BRL currency (displaying "Grátis" when the fee is zero), and the total (subtotal + delivery fee) formatted as BRL currency.
6. THE Platform SHALL display a Coupon input field in the Cart UI that accepts text input; no discount logic SHALL be applied regardless of the value entered, and the field is present for visual purposes only.
7. WHEN the Cart contains zero items, THE Platform SHALL display an informational message (e.g., "Seu carrinho está vazio") and a link or button that closes the Drawer and returns focus to the Store page.
8. THE Platform SHALL maintain Cart state across client-side navigation during the same browser session; items added to the Cart SHALL persist when the Customer navigates between pages without a full page reload.

---

### Requirement 7: Checkout

**User Story:** Como Customer, quero finalizar meu pedido fornecendo dados de entrega e pagamento, para que meu Order seja registrado.

#### Acceptance Criteria

1. IF the Cart contains at least one item, WHEN the Customer proceeds to Checkout, THE Platform SHALL render a Checkout page containing the following required fields: full name, phone number, delivery address, payment method (single-select), and an optional observations text area.
2. WHEN the Customer submits the Checkout form with all required fields filled in and valid, THE Platform SHALL display a Modal with the message "Pedido enviado com sucesso" and record the Order.
3. WHEN the Customer submits the Checkout form with one or more required fields empty, THE Platform SHALL display an inline validation message directly below each invalid field without submitting the form.
4. WHEN the Customer enters a phone number in an invalid format (not matching the Brazilian pattern `(XX) XXXXX-XXXX` or `(XX) XXXX-XXXX`), THE Platform SHALL display an inline validation error on the phone field.
5. THE Platform SHALL offer at least the following payment method options as a selectable group: Dinheiro, Cartão de Crédito, Cartão de Débito, and PIX.
6. WHEN the Customer selects "Dinheiro" as the payment method, THE Platform SHALL display an additional optional "Troco para" field where the Customer can enter the bill denomination for change calculation.
7. WHEN the Customer closes the Order confirmation Modal (by clicking a confirm or close button), THE Platform SHALL clear the Cart and redirect the Customer to the Store page from which the Order was placed.
8. THE Platform SHALL display an Order summary section on the Checkout page showing: all Cart items with name and quantity, subtotal formatted as BRL currency, delivery fee formatted as BRL currency, and the total formatted as BRL currency.
9. WHEN the Checkout form submission encounters an unexpected error, THE Platform SHALL display a Toast or inline error message instructing the Customer to try again, without clearing already-entered form data.

---

### Requirement 8: Painel Administrativo (Admin)

**User Story:** Como operador de uma Store, quero acessar um painel administrativo visual, para que eu possa visualizar e gerenciar as informações da loja.

#### Acceptance Criteria

1. THE Platform SHALL render the Admin panel at the route `/admin` without requiring any authentication credentials or login step.
2. THE Platform SHALL display a persistent Sidebar navigation in the Admin panel containing links to the following sections: Dashboard, Pedidos, Produtos, Categorias, Clientes, Cupons, Entregadores, Configurações, and Aparência; the active section SHALL be visually highlighted.
3. WHEN the Customer navigates to `/admin` or `/admin/dashboard`, THE Platform SHALL display metric cards showing hardcoded or JSON_Data-derived values for: pedidos hoje, total de vendas (BRL), total de clientes, and ticket médio (BRL).
4. WHEN the Dashboard section is active, THE Platform SHALL display a table of the most recent Orders (minimum 5 rows) and a ranked list of the most-sold Products (minimum 3 items), sourced from `orders.json` and `products.json` via their respective Services.
5. WHEN the Dashboard section is active, THE Platform SHALL render at least two chart visualizations (e.g., bar chart for sales by period, line chart for orders by day) populated with static or JSON_Data-derived data.
6. WHEN the Produtos section is active, THE Platform SHALL display a paginated table of all Products from the Products Service, with at least 10 rows per page, and provide a text search input that filters the table by product name in real time.
7. WHEN the Produtos section is active, THE Platform SHALL display filter controls allowing the Admin to filter Products by Category; applying a filter SHALL update the table without a full page reload.
8. WHEN the Admin section is Produtos, THE Platform SHALL display action buttons — "Cadastrar", "Editar" (per row), and "Excluir" (per row) — that open a Modal or form UI for the respective action; no data persistence to JSON_Data is required.
9. WHEN the Admin section is Categorias, THE Platform SHALL display a list of all Categories and provide "Adicionar", "Editar", and "Excluir" controls; all actions SHALL be handled visually (state held in React state) without persisting to JSON_Data.
10. WHEN the Admin section is Pedidos, THE Platform SHALL display a list of Orders from the Orders Service, each showing its current status; a dropdown or segmented control SHALL allow changing the status among: Novo, Preparando, Saiu para entrega, Entregue, and Cancelado.
11. WHEN the Admin changes an Order's status in the Pedidos section, THE Platform SHALL update the displayed status immediately in the UI using local React state, without writing changes back to `orders.json`.

---

### Requirement 9: Perfil do Cliente

**User Story:** Como Customer, quero acessar meu perfil e histórico de pedidos, para que eu possa acompanhar minhas atividades na plataforma.

#### Acceptance Criteria

1. THE Platform SHALL render a Customer profile page at the route `/perfil`.
2. WHEN the Customer navigates to `/perfil`, THE Platform SHALL display four distinct sections: Dados Pessoais, Meus Pedidos, Favoritos, and Endereços.
3. WHEN the Customer navigates to `/perfil`, THE Platform SHALL populate Meus Pedidos with Orders from the Orders Service, displaying for each Order at minimum: an order identifier, the order date, and the current status.
4. WHEN the Customer navigates to `/perfil`, THE Platform SHALL populate Favoritos with Store and Product cards for items flagged as favorites in the Users Service data.
5. WHEN the Customer navigates to `/perfil`, THE Platform SHALL populate Endereços with the address entries from the Users Service data, displaying street, city, and postal code for each entry.
6. WHEN a section (Meus Pedidos, Favoritos, or Endereços) contains no items, THE Platform SHALL display an informational empty-state message within that section (e.g., "Você ainda não fez nenhum pedido") instead of rendering an empty list.

---

### Requirement 10: Componentes Reutilizáveis e Microinterações

**User Story:** Como desenvolvedor, quero um conjunto de componentes reutilizáveis bem definido, para que a interface seja consistente e de fácil manutenção.

#### Acceptance Criteria

1. THE Platform SHALL implement the following reusable components, each as a standalone file in `components/`: `Navbar`, `Footer`, `Sidebar`, `ProductCard`, `StoreCard`, `Banner`, `Modal`, `Drawer`, `Skeleton`, `Loading`, `Toast`, `Alert`, `Breadcrumb`, and `Pagination`.
2. WHEN data is being fetched from a Service (Promise is pending), THE Platform SHALL render the Skeleton variant matching the target content's layout in place of the real content; the Skeleton SHALL be replaced by real content once the Promise resolves.
3. WHEN the user hovers over a `ProductCard` or `StoreCard`, THE Platform SHALL apply a Framer Motion `whileHover` animation (scale between 1.01–1.05, or box-shadow change, or opacity shift) with a transition duration between 150ms and 300ms.
4. WHEN an asynchronous action is triggered by a button (e.g., adding to Cart, submitting a form), THE Platform SHALL display a loading indicator (spinner or animated icon) inside or adjacent to the button from the moment the action starts until it completes or fails; the button SHALL be non-interactive during this period.
5. THE Platform SHALL ensure all interactive components (`Button`, `Input`, `Select`, links, modal triggers) are reachable and operable via keyboard Tab and Enter/Space keys, and meet WCAG 2.1 AA standards for focus visibility and ARIA labeling.
6. THE Platform SHALL render all pages with a layout that adapts correctly to: mobile viewports (320px–767px), tablet viewports (768px–1279px), and desktop viewports (≥1280px); no horizontal scroll SHALL appear on any supported viewport width.

---

### Requirement 11: Qualidade de Código e Performance

**User Story:** Como desenvolvedor, quero que o código siga padrões de qualidade elevados, para que o projeto seja fácil de manter e escalar.

#### Acceptance Criteria

1. THE Platform SHALL define TypeScript interfaces for all data entities (Store, Product, Order, Category, User, Coupon, Cart item) in the `types/` directory; no component or service SHALL use `any` as the type for entity data.
2. THE Platform SHALL implement Next.js `Metadata` exports (or equivalent `generateMetadata` function) on every page route, providing at minimum: `title`, `description`, and `openGraph.title` fields.
3. THE Platform SHALL use `next/image` for every `<img>`-equivalent element that renders Store banners, logos, or Product images; no raw `<img>` tags SHALL be used for these assets.
4. WHEN the application is built, each route's JavaScript bundle SHALL NOT include the source code of components or data exclusive to other routes, as verified by the absence of cross-route module imports at build time.
5. WHEN a page route is accessed on a local network connection of at least 10 Mbps, THE Platform SHALL achieve a Largest Contentful Paint (LCP) of 2 seconds or less as measured by Chrome DevTools Lighthouse in development mode.
6. THE Platform SHALL use only Tailwind CSS utility classes for all styling; custom CSS in `.css` files or inline `style` attributes SHALL only be used when no combination of Tailwind utilities can produce the required animation effect for Framer Motion keyframes.
