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
      <!-- <th scope="col">Status</th> -->
      <th scope="col">Price</th>
      <th scope="col">Model</th>
      <th scope="col" class="d-none d-sm-table-cell">Restock</th>
    </tr>
  </thead>
  <tbody class="inet-gpu-tbody">
    {#each cardList as { id, productUrl, price, name, qty, status, restockDate, restockDays }, i}
      <tr>
        <!-- <td> <span class={getClassByStatus(status)}> {getStatusLabel(status)} </span></td> -->
        <!-- class="d-none d-sm-table-cell" -->
        <td>
          <span class={getClassByStatus(status)}>{new Intl.NumberFormat("en-IN").format(price)} kr</span>
        </td>
        <td><a href={productUrl}>{name}</a></td>
        <td class="d-none d-sm-table-cell">{restockDate}{restockDays ? ` - ${restockDays} days` : ""}</td>
      </tr>
    {/each}
  </tbody>
</table>

<style>
  span {
    padding: 2px 4px;
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

  .store-logo {
    max-height: 38px;
    width: 100px;
    margin-top: 20px;
    background-color: rgb(0, 173, 219);
    padding: 6px;
    border-radius: 6px;
  }
</style>
