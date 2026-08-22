// =============================================
// BANCO DE DADOS DOS JOGOS KIDS
// =============================================

// === BANCO DE PERGUNTAS QUIZ (30+) ===
const QUIZ_BANCO = [
  // FÁCEIS
  { q: "Qual lixeira recebe garrafas PET? 🧴", ops: ["🟦 Azul","🟥 Vermelha","🟨 Amarela","🟩 Verde"], c: 1, exp: "A lixeira VERMELHA é para plásticos como garrafas PET, copos e sacolas!", dif: "easy" },
  { q: "Onde vai o papel e o papelão? 📄", ops: ["🟥 Vermelha","🟦 Azul","🟩 Verde","🟫 Marrom"], c: 1, exp: "A lixeira AZUL é para papel, papelão, jornais e revistas!", dif: "easy" },
  { q: "Latas de alumínio vão em qual lixeira? 🥤", ops: ["🟩 Verde","🟦 Azul","🟨 Amarela","🟥 Vermelha"], c: 2, exp: "A lixeira AMARELA é para metais como latas de alumínio e aço!", dif: "easy" },
  { q: "Garrafas de vidro vão em qual lixeira? 🍶", ops: ["🟨 Amarela","🟩 Verde","🟦 Azul","🟥 Vermelha"], c: 1, exp: "A lixeira VERDE é para vidros como garrafas, potes e frascos!", dif: "easy" },
  { q: "Restos de comida vão em qual lixeira? 🍎", ops: ["🟦 Azul","🟨 Amarela","🟫 Marrom","🟥 Vermelha"], c: 2, exp: "A lixeira MARROM é para lixo orgânico como cascas, restos de comida e folhas!", dif: "easy" },
  { q: "A reciclagem ajuda o meio ambiente?", ops: ["Sim! 🌿","Não 😞","Tanto faz 😐","Não sei 🤷"], c: 0, exp: "Sim! A reciclagem economiza energia, reduz poluição e protege a natureza!", dif: "easy" },
  { q: "O que é coleta seletiva? ♻️", ops: ["Coletar selos","Separar lixo por tipo","Jogar tudo junto","Queimar o lixo"], c: 1, exp: "Coleta seletiva é separar o lixo por tipo (papel, plástico, metal, vidro) para facilitar a reciclagem!", dif: "easy" },
  { q: "Qual desses itens NÃO vai na coleta seletiva? 🚫", ops: ["Lata de alumínio","Garrafa PET","Fralda usada","Jornal velho"], c: 2, exp: "Fraldas e lixo do banheiro vão para o lixo comum (cinza), não para a coleta seletiva!", dif: "easy" },
  { q: "Reciclar papel ajuda a salvar o quê? 🌳", ops: ["Oceanos","Árvores","Montanhas","Desertos"], c: 1, exp: "1 tonelada de papel reciclado economiza o corte de 20 árvores!", dif: "easy" },
  { q: "Pilhas e baterias devem ir para onde? 🔋", ops: ["Lixo comum","Lixo orgânico","Pontos de coleta específicos","Qualquer lixeira"], c: 2, exp: "Pilhas e baterias são resíduos especiais e devem ir para pontos de coleta específicos em supermercados e farmácias!", dif: "easy" },
  // MÉDIOS
  { q: "Quanto tempo uma garrafa PET leva para se decompor? 🧴", ops: ["10 anos","50 anos","400 anos","1.000 anos"], c: 2, exp: "Uma garrafa PET pode levar até 400 anos para se decompor na natureza! Por isso a reciclagem é tão importante!", dif: "medium" },
  { q: "Quanto tempo uma garrafa de vidro leva para sumir na natureza? 🍶", ops: ["100 anos","500 anos","2.000 anos","Mais de 4.000 anos"], c: 3, exp: "O vidro leva mais de 4.000 anos para se decompor! Mas se reciclado, pode virar uma nova garrafa em poucos dias!", dif: "medium" },
  { q: "Quantas vezes o alumínio pode ser reciclado? 🥤", ops: ["1 vez","5 vezes","10 vezes","Infinitas vezes"], c: 3, exp: "O alumínio pode ser reciclado infinitas vezes sem perder qualidade! E economiza 95% de energia!", dif: "medium" },
  { q: "Uma lata de alumínio reciclada pode voltar à prateleira em quanto tempo? ⏱️", ops: ["1 ano","6 meses","60 dias","2 anos"], c: 2, exp: "Incrível! Uma lata de alumínio pode ser reciclada e voltar às prateleiras em apenas 60 dias!", dif: "medium" },
  { q: "Qual material economiza mais energia quando reciclado? ⚡", ops: ["Papel","Plástico","Alumínio","Vidro"], c: 2, exp: "Reciclar alumínio economiza até 95% de energia comparado a produzir alumínio novo!", dif: "medium" },
  { q: "O que é compostagem? 🌱", ops: ["Queimar lixo","Transformar lixo orgânico em adubo","Reciclar plástico","Jogar lixo no mar"], c: 1, exp: "Compostagem é o processo de transformar restos de alimentos e matéria orgânica em adubo para plantas!", dif: "medium" },
  { q: "Quantas toneladas de plástico vão para os oceanos por ano? 🌊", ops: ["1 milhão","8 milhões","100 mil","50 milhões"], c: 1, exp: "Mais de 8 milhões de toneladas de plástico entram nos oceanos todo ano, prejudicando animais marinhos!", dif: "medium" },
  { q: "O que significa a seta triangular no plástico? ♻️", ops: ["É decoração","Indica que pode ser reciclado","Mostra o fabricante","Nada"], c: 1, exp: "O triângulo com setas é o símbolo internacional da reciclagem, indicando que o material pode ser reciclado!", dif: "medium" },
  { q: "Qual desses animais é mais prejudicado pelo plástico nos oceanos? 🐢", ops: ["Leões","Tartarugas marinhas","Pinguins","Ursos polares"], c: 1, exp: "Tartarugas marinhas frequentemente confundem sacolas plásticas com águas-vivas e acabam morrendo ao ingeri-las!", dif: "medium" },
  // DIFÍCEIS
  { q: "Qual porcentagem de energia a reciclagem do alumínio economiza? 💡", ops: ["30%","50%","70%","95%"], c: 3, exp: "Reciclar alumínio economiza impressionantes 95% de energia comparado à produção de alumínio virgem!", dif: "hard" },
  { q: "Qual é o país que mais recicla alumínio no mundo? 🌎", ops: ["Japão","EUA","Brasil","Alemanha"], c: 2, exp: "O Brasil é recordista mundial na reciclagem de latas de alumínio! Reciclamos mais de 97% das latas produzidas!", dif: "hard" },
  { q: "Uma árvore produz oxigênio suficiente para quantas pessoas por dia? 🌳", ops: ["1 pessoa","4 pessoas","10 pessoas","20 pessoas"], c: 1, exp: "Uma árvore produz oxigênio suficiente para 4 pessoas respirarem por dia. Salvar árvores é salvar vidas!", dif: "hard" },
  { q: "Quanto tempo leva para uma fralda descartável se decompor? 👶", ops: ["10 anos","50 anos","200 anos","500 anos"], c: 3, exp: "Uma fralda descartável pode levar até 500 anos para se decompor! Por isso é importante nunca colocar no lixo seletivo.", dif: "hard" },
  { q: "Qual gás produzido pelo lixo orgânico em aterros contribui para o aquecimento global? 🌡️", ops: ["Oxigênio","Metano","Nitrogênio","Hidrogênio"], c: 1, exp: "O metano produzido pelo lixo orgânico em aterros é 25 vezes mais potente que o CO₂ no aquecimento global!", dif: "hard" },
  { q: "O Brasil reciclou qual porcentagem das embalagens PET em 2022? 🧴", ops: ["20%","45%","60%","80%"], c: 2, exp: "O Brasil reciclou cerca de 60% das embalagens PET, o que é excelente comparado à média mundial!", dif: "hard" },
  { q: "Qual é o principal destino do lixo eletrônico no Brasil? 💻", ops: ["Aterros sanitários","Exportado para a China","Reciclado em usinas","Jogado no mar"], c: 0, exp: "Infelizmente, a maior parte do lixo eletrônico brasileiro vai para aterros. Por isso busque pontos de coleta específicos!", dif: "hard" },
  { q: "Qual percentual dos resíduos sólidos no Brasil vai para lixões irregulares? 🗑️", ops: ["10%","20%","40%","60%"], c: 2, exp: "Aproximadamente 40% dos resíduos ainda vão para lixões irregulares no Brasil, contaminando solo e água!", dif: "hard" },
  { q: "Reciclar 1kg de papel economiza quantos litros de água? 💧", ops: ["5L","20L","100L","300L"], c: 3, exp: "Reciclar 1kg de papel economiza impressionantes 300 litros de água! A água é um recurso precioso!", dif: "hard" },
  { q: "Qual é a meta global de redução de plástico de uso único até 2030? 🌍", ops: ["25%","50%","75%","100%"], c: 1, exp: "A ONU estabeleceu meta de reduzir em 50% os plásticos de uso único até 2030 para combater a poluição.", dif: "hard" },
  { q: "Qual material demora mais para se decompor na natureza? ⏳", ops: ["Garrafa PET (400 anos)","Fralda (500 anos)","Vidro (4.000 anos)","Isopor (5.000 anos)"], c: 3, exp: "O isopor (poliestireno) pode demorar até 5.000 anos para se decompor completamente na natureza!", dif: "hard" },
];

// === BANCO VERDADE OU MITO ===
const VM_BANCO = [
  { afirm: "O Brasil é o maior reciclador de latas de alumínio do mundo! 🥇", v: true,  exp: "VERDADE! O Brasil recicla mais de 97% das latas de alumínio, sendo recordista mundial!" },
  { afirm: "Uma garrafa de vidro leva 4.000 anos para se decompor na natureza 🍶", v: true,  exp: "VERDADE! O vidro é um dos materiais que mais demora para sumir da natureza." },
  { afirm: "Papel molhado pode ser reciclado normalmente 📄", v: false, exp: "MITO! Papel molhado ou sujo perde suas fibras e NÃO pode ser reciclado. Sempre descarte papel seco!" },
  { afirm: "Reciclar alumínio economiza 95% de energia ⚡", v: true,  exp: "VERDADE! Comparado à produção de alumínio novo, reciclar economiza 95% de energia!" },
  { afirm: "Fraldas usadas podem ir para a coleta seletiva 👶", v: false, exp: "MITO! Fraldas são rejeitos e devem ir para o lixo comum (cinza), nunca na coleta seletiva!" },
  { afirm: "Tortarugas confundem sacolas plásticas com águas-vivas 🐢", v: true,  exp: "VERDADE! Essa confusão é fatal para muitas tartarugas marinhas. Reduza o uso de sacolas!" },
  { afirm: "Uma lata de alumínio pode voltar à prateleira em 60 dias ⏱️", v: true,  exp: "VERDADE! O ciclo de reciclagem do alumínio é rapidíssimo - apenas 60 dias!" },
  { afirm: "Isopor pode ser colocado em qualquer lixeira colorida 🟥", v: false, exp: "MITO! Isopor é problemático para reciclar e deve ser levado a pontos específicos de coleta." },
  { afirm: "Reciclando 1kg de papel economizamos o corte de 20 árvores 🌳", v: false, exp: "MITO! 1 TONELADA de papel reciclado poupa 20 árvores, não 1kg. Mas ainda assim vale muito!" },
  { afirm: "O lixo orgânico produz gás metano em aterros, aquecendo o planeta 🌡️", v: true,  exp: "VERDADE! O metano é 25x mais potente que CO₂ no efeito estufa. Por isso compostar é importante!" },
  { afirm: "Pilhas e baterias podem ir na lixeira amarela 🔋", v: false, exp: "MITO! Pilhas e baterias são resíduos especiais e devem ir em pontos de coleta específicos!" },
  { afirm: "O plástico PET pode ser transformado em fibra para roupas 👕", v: true,  exp: "VERDADE! Garrafas PET recicladas viram fios de poliéster usados em roupas, carpetes e muito mais!" },
];

// === ITENS PARA O JOGO SALVA A CIDADE ===
const SEPARAR_ITENS = [
  { emoji:"📄", nome:"Jornal",        lixeira:"azul",     dica:"Papel vai na lixeira AZUL" },
  { emoji:"📦", nome:"Caixa",         lixeira:"azul",     dica:"Papelão vai na lixeira AZUL" },
  { emoji:"🧴", nome:"Shampoo",       lixeira:"vermelha", dica:"Plástico vai na lixeira VERMELHA" },
  { emoji:"🍼", nome:"Garrafa PET",   lixeira:"vermelha", dica:"PET vai na lixeira VERMELHA" },
  { emoji:"🛍️", nome:"Sacola",        lixeira:"vermelha", dica:"Sacola plástica vai na lixeira VERMELHA" },
  { emoji:"🥫", nome:"Latinha",       lixeira:"amarela",  dica:"Metal vai na lixeira AMARELA" },
  { emoji:"🔩", nome:"Parafuso",      lixeira:"amarela",  dica:"Metal vai na lixeira AMARELA" },
  { emoji:"🍶", nome:"Garrafa Vidro", lixeira:"verde",    dica:"Vidro vai na lixeira VERDE" },
  { emoji:"🫙", nome:"Pote Vidro",    lixeira:"verde",    dica:"Vidro vai na lixeira VERDE" },
  { emoji:"🍎", nome:"Maçã",          lixeira:"marrom",   dica:"Orgânico vai na lixeira MARROM" },
  { emoji:"🍌", nome:"Banana",        lixeira:"marrom",   dica:"Orgânico vai na lixeira MARROM" },
  { emoji:"☕", nome:"Borra café",    lixeira:"marrom",   dica:"Orgânico vai na lixeira MARROM" },
  { emoji:"🩹", nome:"Curativo",      lixeira:"cinza",    dica:"Rejeito vai na lixeira CINZA" },
  { emoji:"🧻", nome:"Papel Hig.",    lixeira:"cinza",    dica:"Papel higiênico vai na lixeira CINZA" },
  { emoji:"💊", nome:"Remédio",       lixeira:"cinza",    dica:"Medicamentos vão em pontos especiais! Na dúvida: cinza" },
  { emoji:"📰", nome:"Revista",       lixeira:"azul",     dica:"Papel/revista vai na lixeira AZUL" },
  { emoji:"🥤", nome:"Copo Plást.",   lixeira:"vermelha", dica:"Plástico vai na lixeira VERMELHA" },
  { emoji:"🔋", nome:"Pilha",         lixeira:"cinza",    dica:"Pilhas devem ir em pontos especiais!" },
  { emoji:"🍷", nome:"Garrafa Vinho", lixeira:"verde",    dica:"Vidro vai na lixeira VERDE" },
  { emoji:"🥚", nome:"Casca Ovo",     lixeira:"marrom",   dica:"Casca de ovo é orgânico: lixeira MARROM" },
];

// === ITENS MESTRE DAS LIXEIRAS ===
const LIXEIRAS_ITENS = [
  { emoji:"📄", nome:"Papel",         lixeira:"azul",     cor:"#3b82f6" },
  { emoji:"📦", nome:"Papelão",       lixeira:"azul",     cor:"#3b82f6" },
  { emoji:"📰", nome:"Jornal",        lixeira:"azul",     cor:"#3b82f6" },
  { emoji:"🧴", nome:"Plástico",      lixeira:"vermelha", cor:"#ef4444" },
  { emoji:"🍼", nome:"Garrafa PET",   lixeira:"vermelha", cor:"#ef4444" },
  { emoji:"🥤", nome:"Copo Plást.",   lixeira:"vermelha", cor:"#ef4444" },
  { emoji:"🥫", nome:"Latinha",       lixeira:"amarela",  cor:"#eab308" },
  { emoji:"🔩", nome:"Metal",         lixeira:"amarela",  cor:"#eab308" },
  { emoji:"🍶", nome:"Garrafa Vidro", lixeira:"verde",    cor:"#22c55e" },
  { emoji:"🫙", nome:"Pote Vidro",    lixeira:"verde",    cor:"#22c55e" },
  { emoji:"🍎", nome:"Casca Fruta",   lixeira:"marrom",   cor:"#a16207" },
  { emoji:"🩹", nome:"Curativo",      lixeira:"cinza",    cor:"#6b7280" },
];

const LIXEIRAS_CONFIG = [
  { id:"azul",    emoji:"🟦", nome:"Azul (Papel)",    bg:"#3b82f6" },
  { id:"vermelha",emoji:"🟥", nome:"Verm. (Plástico)",bg:"#ef4444" },
  { id:"amarela", emoji:"🟨", nome:"Amar. (Metal)",   bg:"#eab308" },
  { id:"verde",   emoji:"🟩", nome:"Verde (Vidro)",   bg:"#22c55e" },
  { id:"marrom",  emoji:"🟫", nome:"Marrom (Orgânico)",bg:"#a16207"},
  { id:"cinza",   emoji:"⬛", nome:"Cinza (Rejeito)", bg:"#6b7280" },
];

// === FATOS INCRÍVEIS ===
const FATOS_LISTA = [
  { emoji:"🥤", titulo:"Super Alumínio!", texto:"Uma lata de alumínio pode ser reciclada e <strong>voltar às prateleiras em apenas 60 dias</strong>! E pode ser reciclada <strong>infinitas vezes</strong> sem perder qualidade! 🤯" },
  { emoji:"🌳", titulo:"Papel Salva Árvores!", texto:"Com <strong>1 tonelada de papel reciclado</strong> economizamos o corte de <strong>20 árvores!</strong> Quando você recicla papel, está salvando uma floresta inteira! 🌱" },
  { emoji:"🍶", titulo:"Vidro Eterno!", texto:"O vidro que vai para o lixo comum leva <strong>mais de 4.000 anos</strong> para se decompor. Mas quando reciclado, pode se tornar uma garrafa nova em poucos dias! ♻️" },
  { emoji:"🧴", titulo:"Plástico Mágico!", texto:"Sabia que <strong>garrafas PET recicladas</strong> podem virar casaco de frio, banco de praça, carpete e até peças de roupa? Uma garrafa se transforma completamente! 👕" },
  { emoji:"🌊", titulo:"Oceanos em Perigo!", texto:"Mais de <strong>8 milhões de toneladas</strong> de plástico entram nos oceanos todo ano. Tartarugas, golfinhos e peixes morrem por isso. <strong>Você pode ajudar reciclando!</strong> 🐢" },
  { emoji:"🇧🇷", titulo:"Brasil Recordista!", texto:"O Brasil é o <strong>maior reciclador de alumínio do mundo</strong>! Reciclamos mais de 97% das latas produzidas. Isso é motivo de orgulho! 🏆" },
  { emoji:"⚡", titulo:"Energia Incrível!", texto:"Reciclar alumínio economiza <strong>95% de energia</strong> comparado a produzir alumínio novo. É como se uma única lata reciclada mantivesse uma lâmpada acesa por 20 horas! 💡" },
  { emoji:"🌡️", titulo:"Lixo e Clima!", texto:"O lixo orgânico em aterros produz <strong>metano</strong>, um gás que aquece o planeta 25 vezes mais que o CO₂. Compostar o lixo de casa ajuda a combater a crise climática! 🌿" },
  { emoji:"💧", titulo:"Água Preciosa!", texto:"Reciclar <strong>1kg de papel economiza 300 litros de água</strong>! A água é o recurso mais precioso da Terra, e a reciclagem ajuda a preservá-la para as futuras gerações. 💙" },
  { emoji:"👕", titulo:"Roupa do Futuro!", texto:"Sabia que a <strong>roupa que você usa</strong> pode ter sido feita de garrafas PET recicladas? 1 jaqueta de frio usa em média 25 garrafas PET! A moda sustentável já é realidade! 🌍" },
];

// === PALAVRAS PARA O CAÇA-PALAVRAS ===
const CACA_PALAVRAS = [
  "RECICLAR", "PAPEL", "PLASTICO", "METAL", "VIDRO",
  "ORGANICO", "LIXEIRA", "PLANETA", "NATUREZA", "VERDE",
];

// === RANKING FAKE ===
const RANKING_FAKE = [
  { nome:"🐸 Recico Master",  pts: 2450, nivel:"Floresta 🌳",    jogos: 42 },
  { nome:"🦋 EcoWarrior",     pts: 2180, nivel:"Floresta 🌳",    jogos: 38 },
  { nome:"🐢 VerdinhoPro",    pts: 1920, nivel:"Árvore 🌲",      jogos: 31 },
  { nome:"🦁 ReciKing",       pts: 1750, nivel:"Árvore 🌲",      jogos: 28 },
  { nome:"🐼 PlanetaSalvo",   pts: 1490, nivel:"Broto 🌿",       jogos: 22 },
  { nome:"🐬 AquaRecicla",    pts: 1230, nivel:"Broto 🌿",       jogos: 18 },
  { nome:"🦊 EcoFox",         pts:  980, nivel:"Muda 🌱",        jogos: 14 },
  { nome:"🐴 GreenPony",      pts:  760, nivel:"Muda 🌱",        jogos: 11 },
];
