<script>
  export default {
    props: {
      title: {
        type: String,
        required: true
      }
    }
  }
</script>


<template>
    <div class="card">
      <div class="card-header card-w-700">
        {{ title }}
      </div>
      
        <slot></slot>
    </div>
</template>