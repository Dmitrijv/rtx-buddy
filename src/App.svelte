<script>
  import GpuTable from "./GpuTable.svelte";

  import { onMount } from "svelte";

  export let cardList = [];
  export let isLoading = true;

  async function loadLatestData() {
    isLoading = true;
    //await fetch("http://dmitrijv.se/projects/rtx-buddy/php/cards.php")
    await fetch("php/cards.php")
      .then((res) => res.json())
      .then((data) => {
        cardList = data;
      });

    isLoading = false;
  }

  onMount(async () => {
    await loadLatestData();
  });
</script>

<main>
  <div class="nav-container">
    <div class="nav">
      <h1>RTX 3080 Buddy</h1>
      <button type="button" class="btn btn-light shadow-none" on:click={loadLatestData}>Update</button>
    </div>
  </div>
  <div class="table-container">
    {#if isLoading}
      <!-- Spinner -->
      <div class="text-center">
        <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    {:else}
      <GpuTable {cardList} />
    {/if}
  </div>
</main>

<style>
  .nav {
    display: flex;
    justify-content: space-between;

    width: 100%;
    max-width: 700px;
    padding: 10px;
    margin: 0 auto;
  }

  button.btn-light {
    background-color: #f0f0f0;
  }

  .nav h1 {
    font-size: 26px;
    line-height: 1.3;
    color: #323c46;
    padding: 0;
    margin: 0;
    text-align: left;
  }

  .nav-container {
    background-color: white;
    margin-bottom: 30px;
    box-shadow: 0 -0.4rem 0.4rem 0.2rem rgb(0 0 0 / 50%);
  }

  .table-container {
    width: 100%;
    max-width: 700px;
    box-shadow: rgba(149, 157, 165, 0.15) 0px 3px 6px 0px;
    background-color: white;
    margin: 10px auto;
    padding: 10px;
    border-radius: 6px;
  }
</style>
