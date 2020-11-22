<template>
  <v-card>
    <v-card-title>
        <v-row>
          <v-col cols="6">
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Search"
              single-line
              hide-details
            ></v-text-field>
          </v-col>
        </v-row>
    </v-card-title>

    <v-data-table
        :headers="[
        { text: 'Affiliate', value: 'id' },
        { text: 'name', value: 'name' },
        { text: 'total points', value: 'total' },
        { text: 'paid', value: 'paid' },
        { text: 'unpaid', value: 'unpaid' },
        { text: 'last action', value: 'created_at' },
        { text: 'actions', value: 'actions' },

      ]"
        :items="dataTable"
        :search="search"
    >

      <template v-slot:item.actions="{ item }">
        <button type="button" class="btn btn-link" @click="open(item)">
          open
        </button>
      </template>

    </v-data-table>


  </v-card>

</template>

<script>
export default {

  props: {
    dataTable: {
      type: Array,
    },
  },

  data() {
    return {
      search: null,
    }
  },

  methods : {
    open(item) {
      console.log(window.location.href);
      const current = window.location.href;
      window.location.href = current + '/' + item.id
    }
  }

}
</script>
