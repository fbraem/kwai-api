<template>
    <div>
        <v-text-field
            :error-messages="errors"
            append-icon="event"
            :append-icon-cb="doShowPicker"
            :hint="dateFormat"
            :value="value"
            @input="updateValue($event)"
            v-bind="$attrs"
            >
        </v-text-field>
        <v-dialog
            persistent
            v-model="showPicker"
            lazy
            full-width>
            <v-date-picker
                v-model="date"
                scrollable
                actions
                :date-format="formatDate"
                :allowed-dates="allowedDates"
                >
              <template scope="{ save, cancel }">
                <v-card-actions>
                  <v-btn flat primary @click.native="cancel()">Cancel</v-btn>
                  <v-btn flat primary @click.native="save()">Save</v-btn>
                </v-card-actions>
              </template>
            </v-date-picker>
        </v-dialog>
    </div>
</template>

<script>
    import moment from 'moment';

    export default {
        props : [
            '$v',
            'value',
            'errors',
            'allowedDates'
        ],
        data() {
            var currentDate = null;
            if (this.value) {
                var date = moment(this.value, 'L');
                if (date.isValid()) currentDate = date.format('YYYY-MM-DD');
            }
            return {
                date : currentDate,
                showPicker : false
            }
        },
        computed : {
            dateFormat() {
                return 'Format ' + moment.localeData().longDateFormat('L');
            }
        },
        watch : {
            date(nv) {
                if (nv) {
                    this.updateValue(moment(nv, 'YYYY-MM-DD').format('L'))
                } else {
                    this.updateValue(null);
                }
            }
        },
        methods : {
            formatDate() {
                return moment(this.date).format('L');
            },
            doShowPicker() {
                if (this.$v.$invalid) {
                    this.date = null;
                }
                this.showPicker = !this.showPicker;
                return this.showPicker;
            },
            updateValue(value) {
                this.$v.$touch();
                this.$emit('input', value);
            }
        }

    };
</script>
