<script>
  import GpuTable from "./GpuTable.svelte";

  import { onMount } from "svelte";
  import BarSpinner from "./BarSpinner.svelte";

  export let cardList = [];
  export let loading = true;
  export let refreshing = false;

  async function updateCardList(isRefresh = false) {
    if (loading || refreshing) return;
    refreshing = isRefresh;
    // await fetch("http://dmitrijv.se/projects/rtx-buddy/php/cards.php")
    await fetch("php/cards.php")
      .then((res) => res.json())
      .then((data) => {
        cardList = data;
        loading = false;
        refreshing = false;
      });
  }

  onMount(async () => {
    await updateCardList();
  });
</script>

<main>
  <!-- Header -->
  <div class="nav-container">
    <div class="nav">
      <h1>RTX Buddy</h1>
      <button type="button" class="btn btn-light shadow-none" on:click={() => updateCardList(true)}>
        {#if refreshing}
          <BarSpinner bars={4} />
        {:else}
          Update
        {/if}
      </button>
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

<style>
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

  button.btn-light {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 86px;
    height: 34px;
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
    padding: 8px;
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
</style>
