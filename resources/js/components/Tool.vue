<template>
  <div>
    <heading class="mb-6 flex">
      Translations
      <select
        class="ml-auto translation-switch"
        v-if="locales.length > 0"
        v-model="selected_locale"
      >
        <option v-for="locale in locales" :key="locale" :value="locale">{{ locale.toUpperCase() }}</option>
      </select>
    </heading>

    <card class="translation-card" style="min-height: 300px">
      <div class="actions-field">
        <input
          type="text"
          placeholder="Search for translation..."
          class="form-control-search"
          v-model="search_term"
        >
      </div>
      <div class="translation-list" v-if="filtered_list">
        <TranslationItem
          v-for="(trans, index) in filtered_list"
          :key="index"
          :trans="trans"
          @updateTranslation="updateTranslation"
          @setTranslation="setTranslation"
          @saveTranslation="saveTranslation"
        ></TranslationItem>
      </div>
    </card>
  </div>
</template>

<script>
import TranslationItem from "./TranslationItem";

export default {
  components: { TranslationItem },
  data() {
    return {
      tree: null,
      locales: [],
      groups: [],
      selected_locale: null,
      search_term: ""
    };
  },
  computed: {
    filtered_list() {
      const selected_locale = this.selected_locale;
      if (this.tree && selected_locale) {
        const search_term = this.search_term;
        const locale_tree = this.tree[selected_locale];
        const reg = search_term
          ? new RegExp(search_term.toLowerCase(), "gi")
          : false;

        const list = [];
        for (let group in locale_tree) {
          const tree = locale_tree[group];
          for (let key in tree) {
            const x = tree[key];
            const { new: newValue, old } = x;
            const full_key = `${group}.${key}`;
            const value = newValue ? newValue : old;
            if (
              typeof value === "object" ||
              (reg && !value.match(reg) && !full_key.match(reg))
            ) {
              continue;
            }
            list.push({
              ...x,
              key,
              group,
              locale: selected_locale,
              full_key
            });
          }
        }

        return list;
      }
      return null;
    }
  },
  methods: {
    updateTransItem(trans, newData = {}) {
      const { locale, group, key } = trans;
      this.tree = {
        ...this.tree,
        [locale]: {
          ...this.tree[locale],
          [group]: {
            ...this.tree[locale][group],
            [key]: {
              ...this.tree[locale][group][key],
              ...newData
            }
          }
        }
      };
    },
    saveTranslation(trans) {
      const { locale, group, key, full_key, new: newValue } = trans;
      this.updateTransItem(trans, { loading: true });

      Nova.request()
        .post("/nova-vendor/nova-translations/update", {
          locale,
          key: full_key,
          text: newValue
        })
        .then(({ data: rsp = {} }) => {
          this.updateTransItem(trans, { loading: false, has_changed: false, new: newValue, old: rsp.original });

          this.$toasted.show('Translation is saved!', { type: 'success' })
        }).catch(error => {
          this.$toasted.show('Something went wrong!', { type: 'error' })
        });
    },
    updateTranslation(trans, evt) {
      let value = evt.target.value;

      this.updateTransItem(trans, {
        new: value,
        has_changed: true
      });
    },
    setTranslation(trans, params = {}){
      return this.updateTransItem(trans, params);
    }
  },
  mounted() {
    Nova.request()
      .get("/nova-vendor/nova-translations/list")
      .then(({ data: rsp = {} }) => {
        const { data: { locales = [], groups = [], tree = {} } = {} } = rsp;

        this.tree = tree;
        this.locales = locales;
        this.groups = groups;

        this.selected_locale = locales[0];
      });
  }
};
</script>

<style lang="scss" scoped>
.translation-switch {
  font-size: 16px;
  min-width: 100px;
}
.actions-field {
  .form-control-search {
    display: block;
    width: 100%;
    padding: 7px 12px;
    background: #fff;
    border: 1px solid #cccccc;
    border-radius: 0px;
    outline: none;
    margin: -1px -1px 0;
    height: 40px;
  }
}
.translation-list {
  max-height: calc(100vh - 250px);
  overflow-y: scroll;
  -webkit-overflow-scrolling: touch;
}
</style>
