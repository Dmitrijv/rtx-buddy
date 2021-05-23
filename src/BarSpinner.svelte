<script>
  export let bars = 3;
</script>

<div class="bar-spinner">
  {#if bars >= 1}
    <div class="rect1" />
  {/if}
  {#if bars >= 2}
    <div class="rect2" />
  {/if}
  {#if bars >= 3}
    <div class="rect3" />
  {/if}
  {#if bars >= 4}
    <div class="rect4" />
  {/if}
  {#if bars >= 5}
    <div class="rect5" />
  {/if}
</div>

<style>
  .bar-spinner {
    margin: 6px auto;
    width: 60px;
    height: 30px;
    text-align: center;
    font-size: 10px;
    user-select: none;
  }

  .bar-spinner > div {
    background-color: rgb(139, 170, 98);
    height: 100%;
    width: 6px;
    display: inline-block;

    -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
    animation: sk-stretchdelay 1.2s infinite ease-in-out;
  }

  .bar-spinner .rect2 {
    -webkit-animation-delay: -1.1s;
    animation-delay: -1.1s;
  }

  .bar-spinner .rect3 {
    -webkit-animation-delay: -1s;
    animation-delay: -1s;
  }

  .bar-spinner .rect4 {
    -webkit-animation-delay: -0.9s;
    animation-delay: -0.9s;
  }

  .bar-spinner .rect5 {
    -webkit-animation-delay: -0.8s;
    animation-delay: -0.8s;
  }

  @-webkit-keyframes sk-stretchdelay {
    0%,
    40%,
    100% {
      -webkit-transform: scaleY(0.4);
    }
    20% {
      -webkit-transform: scaleY(1);
    }
  }

  @keyframes sk-stretchdelay {
    0%,
    40%,
    100% {
      transform: scaleY(0.4);
      -webkit-transform: scaleY(0.4);
    }
    20% {
      transform: scaleY(1);
      -webkit-transform: scaleY(1);
    }
  }
</style>
