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
        { text: 'user', value: 'user_id' },
        { text: 'name', value: 'user_name' },
        { text: 'date', value: 'created_at' },
        { text: 'points', value: 'points' },
        { text: 'action', value: 'actionName' },
        { text: 'paid', value: 'paid' },
        { text: 'paid', value: 'paidSearchable', align: ' d-none', class: 'hidden',},
      ]"
        :items="dataTable"
        :search="search"
    >
      <template v-slot:item.paid="{ item }">
        <v-chip
          v-if="item.paid"
          color="green" x-small outlined>{{item.paidSearchable}}
        </v-chip>
        <v-chip
          v-if="!item.paid"
          color="orange" x-small outlined>{{item.paidSearchable}}
        </v-chip>
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

  }

}
</script>
