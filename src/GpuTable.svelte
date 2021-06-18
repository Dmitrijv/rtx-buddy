<script>
  export let cardList = [];

  function getClassByStatus(status) {
    if (status == 0) return "in-stock";
    else if (status == 1) return "in-stock";
    else if (status == 2) return "incoming";
    return "sold-out";
  }

  function getStatusLabel(status) {
    if (status == 0) return "in stock";
    else if (status == 1) return "in stock";
    else if (status == 2) return "incoming";
    else if (status == 3) return "delayed";
    else if (status == 4) return "sold out";
    else if (status == 5) return "blocked";
    return "n/a";
  }
</script>

<!-- GPU Table -->
<table class="table table-sm table-hover">
  <thead>
    <tr>
      <th scope="col">SEK</th>
      <th scope="col">Model</th>
      <th scope="col" class="d-none d-sm-table-cell">Restock</th>
      <th scope="col" class="d-none d-sm-table-cell">Days</th>
      <th scope="col" class="d-none d-sm-table-cell">Source</th>
    </tr>
  </thead>
  <tbody class="inet-gpu-tbody">
    {#each cardList as { id, url, price, name, qty, status, restockDate, restockDays, source }, i}
      <tr>
        <td>
          <span class={getClassByStatus(status)}>{new Intl.NumberFormat("en-IN").format(Math.floor(price))}</span>
        </td>
        <td><a href={url}>{@html name.replace(" Ti ", "<strong> Ti </strong>")}</a></td>
        <td class="d-none d-sm-table-cell">{restockDate}</td>
        <td class="d-none d-sm-table-cell">{restockDays}</td>
        <td class="d-none d-sm-table-cell logo-cell">
          <a href={url}>
            <img
              class="source-logo {source == 'prisjakt' ? 'bg-prisjakt' : ''}"
              src={`image/svg/logo-${source}.svg`}
              alt={source}
            />
          </a>
        </td>
      </tr>
    {/each}
  </tbody>
</table>

<style>
  .table-hover > tbody > tr:hover {
    --bs-table-accent-bg: none !important;
    background-color: whitesmoke !important;
  }

  span {
    padding: 2px 4px;
  }

  .logo-cell {
    display: flex;
    justify-content: flex-start;
    align-items: center;
  }

  .bg-prisjakt {
    background: rgb(0, 173, 219);
  }

  .in-stock {
    font-weight: bold;
    color: white;
    background-color: rgb(139, 170, 98);
  }

  .incoming {
    font-weight: bold;
    color: white;
    background-color: rgb(222, 186, 90);
  }

  .sold-out {
    font-weight: bold;
    color: white;
    background-color: rgb(164, 68, 65);
  }

  a {
    text-decoration: none;
  }

  img.source-logo {
    max-height: 16px;
    max-width: 50px;
    border-radius: 4px;
  }
</style>
