<?php require_once 'app/view/comuns/admin_cabecalho.php'; ?>

      <div class="dashboard-card">
        <div class="card-header">
          <h2>Horários de Coleta por Região</h2>
        </div>
        <table class="admin-table">
          <thead>
            <tr>
              <th>Bairro / Região</th>
              <th>Dias da Semana</th>
              <th>Horário</th>
              <th>Materiais</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Centro</td>
              <td>Terça e Sexta</td>
              <td>07h – 12h</td>
              <td>Papel, Plástico, Metal, Vidro</td>
              <td>
                <button class="btn-acao editar" onclick="editarColeta('Centro')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
            <tr>
              <td>Bairro Novo</td>
              <td>Segunda e Quinta</td>
              <td>08h – 13h</td>
              <td>Papel, Plástico, Metal</td>
              <td>
                <button class="btn-acao editar" onclick="editarColeta('Bairro Novo')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
            <tr>
              <td>Vila Nova</td>
              <td>Quarta e Sábado</td>
              <td>07h – 11h</td>
              <td>Papel, Plástico, Vidro</td>
              <td>
                <button class="btn-acao editar" onclick="editarColeta('Vila Nova')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
            <tr>
              <td>Jardim América</td>
              <td>Terça e Sexta</td>
              <td>13h – 18h</td>
              <td>Papel, Plástico, Metal, Vidro</td>
              <td>
                <button class="btn-acao editar" onclick="editarColeta('Jardim América')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
            <tr>
              <td>Bairro Escolar</td>
              <td>Quinta</td>
              <td>07h – 12h</td>
              <td>Papel, Plástico</td>
              <td>
                <button class="btn-acao editar" onclick="editarColeta('Bairro Escolar')">✏️ Editar</button>
                <button class="btn-acao excluir" onclick="excluirItem(this)">🗑️ Excluir</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

  <!-- MODAL CADASTRAR/EDITAR COLETA -->
  <div id="modalColeta" class="modal" style="display:none;">
    <div class="modal-content">
      <div class="modal-top">
        <h3 id="modalColetaTitulo">Cadastrar Horário de Coleta</h3>
        <button class="modal-close" onclick="fecharModalAdmin()">✕</button>
      </div>
      <div class="form-group">
        <label>Bairro / Região *</label>
        <input type="text" id="coletaBairro" placeholder="Ex: Centro">
      </div>
      <div class="form-group">
        <label>Dias da Semana *</label>
        <div class="dias-check">
          <label><input type="checkbox" value="seg"> Segunda</label>
          <label><input type="checkbox" value="ter"> Terça</label>
          <label><input type="checkbox" value="qua"> Quarta</label>
          <label><input type="checkbox" value="qui"> Quinta</label>
          <label><input type="checkbox" value="sex"> Sexta</label>
          <label><input type="checkbox" value="sab"> Sábado</label>
        </div>
      </div>
      <div class="form-group">
        <label>Horário Início</label>
        <input type="time" id="coletaHoraInicio" value="07:00">
      </div>
      <div class="form-group">
        <label>Horário Fim</label>
        <input type="time" id="coletaHoraFim" value="12:00">
      </div>
      <div class="form-group">
        <label>Materiais Recolhidos</label>
        <div class="dias-check">
          <label><input type="checkbox" value="papel" checked> Papel</label>
          <label><input type="checkbox" value="plastico" checked> Plástico</label>
          <label><input type="checkbox" value="metal" checked> Metal</label>
          <label><input type="checkbox" value="vidro"> Vidro</label>
        </div>
      </div>
      <div class="modal-actions">
        <button class="btn-primary" onclick="salvarColeta()">Salvar</button>
        <button class="btn-secondary" onclick="fecharModalAdmin()">Cancelar</button>
      </div>
    </div>
  </div>

<?php require_once 'app/view/comuns/admin_rodape.php'; ?>
