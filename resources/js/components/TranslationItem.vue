<template>
  <div class="translation flex items-stretch">
    <div class="translation__key">{{ trans.full_key }}</div>
    <div class="translation__text flex-grow">
      <textarea
        type="text"
        :value="trans.new || trans.old"
        @input="$emit('updateTranslation', trans, $event)"
        :disabled="trans.loading"
        class="translation__text__value"
      ></textarea>
      <div class="translation__text__helper" v-if="trans.new && trans.new !== trans.old">
        <p>
          <b>Old text:</b>
          <i>{{ trans.old }}</i>
        </p>
        <p class="text-right">
          <button type="button" class="btn btn-default btn-outline btn-sm" @click="$emit('setTranslation', trans, {new: '', has_changed: true})">Roll back</button>
        </p>
      </div>
    </div>
    <div class="translation__actions">
      <button
        class="btn btn-default btn-primary btn-sm"
        @click="$emit('saveTranslation', trans)"
        :disabled="!trans.has_changed || trans.loading"
      >Gem</button>
    </div>
  </div>
</template>

<script>
export default {
  props: ["trans"]
};
</script>

<style lang="scss" scoped>
.translation {
  background: #eeeeee;

  &:nth-child(2n) {
    background: #fefefe;
  }

  &__key {
    width: 35%;
    padding: 6px 10px;
    font-size: 13px;
    font-weight: bold;
  }
  &__text {
    flex-grow: 1;
    padding: 6px;

    &__value {
      width: 100%;
      border-radius: 0px;
      padding: 6px 10px;
      border: 1px solid #cccccc;
      outline: none;
    }
    &__helper {
      padding: 6px 0 6px;
    }
  }
  &__actions {
    width: 100px;
    padding: 6px 10px;
  }
}
.text-right {
  text-align: right;
}
.translation__text__helper .btn {

}
</style>
