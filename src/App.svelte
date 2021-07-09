<script>
  import { onMount, onDestroy } from "svelte";
  import BarSpinner from "./BarSpinner.svelte";
  import GpuTable from "./GpuTable.svelte";

  export let cardList = [];
  export let inStockCount = 0;
  export let incomingCount = 0;
  export let timestamp = "loading";
  export let loading = true;
  export let refreshing = false;

  async function updateCardList(isRefresh = false) {
    if (refreshing) return;
    refreshing = isRefresh;
    const now = new Date().toLocaleTimeString("en-US", { hour12: false, hour: "numeric", minute: "numeric" });
    await fetch("php/cards.php")
      .then((res) => res.json())
      .then((data) => {
        cardList = data;
        inStockCount = cardList.filter((c) => c.status <= 1).length;
        incomingCount = cardList.length - inStockCount;
        loading = false;
        refreshing = false;
        updateDocumentTitle(cardList);
        timestamp = now;
      })
      .catch((error) => {
        console.error("Network request error: ", error);
        cardList = [];
        loading = false;
        refreshing = false;
        timestamp = "ERR";
      });
  }

  function updateDocumentTitle(cardList) {
    if (cardList.length > 0 && cardList[0].status <= 1) {
      const bestCard = cardList[0];
      const bestPrice = new Intl.NumberFormat("en-IN").format(Math.floor(bestCard.price));
      document.title = `${bestPrice} - ${bestCard.name}`;
    } else {
      document.title = "RTX 3080";
    }
  }

  let interval;

  onMount(async () => {
    await updateCardList();
    interval = setInterval(async () => {
      await updateCardList(true);
    }, 30000);
  });

  onDestroy(() => clearInterval(interval));
</script>

<main>
  <!-- Header <span class="tracked-model-label">RTX 3080</span> -->
  <div class="nav-container">
    <div class="nav">
      <h1>
        <span class="tracked-model-label">RTX 3080</span>
        <span class="in-stock">{inStockCount}</span> / <span class="incoming">{incomingCount}</span>
      </h1>
      <div class="btn btn-light shadow-none">
        {#if refreshing}
          <BarSpinner bars={4} />
        {:else}
          {timestamp}
        {/if}
      </div>
    </div>
  </div>
  <!-- Table -->
  <div class="table-container">
    {#if loading}
      <div class="table-spinner">
        <BarSpinner bars={5} />
      </div>
    {:else}
      <GpuTable {cardList} />
    {/if}
  </div>
</main>

<style lang="scss">
  .tracked-model-label {
    @media (max-width: 576px) {
      display: none;
    }
  }

  .nav-container {
    background-color: white;
    margin-bottom: 30px;
    box-shadow: 0 -0.4rem 0.4rem 0.2rem rgb(0 0 0 / 50%);
    padding: 0.25rem;
  }

  .nav {
    display: flex;
    justify-content: space-between;
    align-items: center;

    width: 100%;
    max-width: 650px;
    padding: 4px;
    margin: 0 auto;
  }

  .btn-light {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 86px;
    height: 34px;
    cursor: default;
  }

  .nav h1 {
    font-size: 26px;
    line-height: 1.3;
    color: #323c46;
    padding: 0;
    margin: 0;
    text-align: left;
  }

  .table-spinner {
    padding: 20px 0;
  }

  .table-container {
    width: 100%;
    max-width: 650px;
    box-shadow: rgba(149, 157, 165, 0.15) 0px 3px 6px 0px;
    background-color: white;
    margin: 10px auto;
    padding: 4px;
    border-radius: 6px;
  }

  .in-stock {
    font-weight: bold;
    color: rgb(139, 170, 98);
  }

  .incoming {
    font-weight: bold;
    color: rgb(222, 186, 90);
  }

  // .sold-out {
  //   font-weight: bold;
  //   color: rgb(164, 68, 65);
  // }
</style>
