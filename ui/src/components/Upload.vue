<template>
  <!-- Based on code from https://github.com/chybie/file-upload-vue -->
  <!-- eslint-disable max-len -->
  <div>
    <!--UPLOAD-->
    <form
      enctype="multipart/form-data"
      novalidate
      v-if="isInitial || isSaving"
    >
      <div class="dropbox">
        <input
          type="file"
          :name="field"
          :disabled="isSaving"
          @change="filesChange($event.target.name, $event.target.files); fileCount = $event.target.files.length"
          accept="text/csv*"
          class="input-file"
        >
          <p v-if="isInitial">
            Drag your file(s) here to begin<br /> or click to browse
          </p>
          <p v-if="isSaving">
            Uploading {{ fileCount }} files...
          </p>
      </div>
    </form>
    <div v-if="isSuccess">
      File is uploaded!
    </div>
    <div v-if="isFailed">
      {{ uploadError }}
    </div>
  </div>
</template>

<script>
const STATUS_INITIAL = 0;
const STATUS_SAVING = 1;
const STATUS_SUCCESS = 2;
const STATUS_FAILED = 3;

import axios from '@/js/http';

export default {
  props: {
    url: {
      type: String,
      required: true
    },
    field: {
      type: String,
      default: 'files'
    },
  },
  data() {
    return {
      uploadedFiles: [],
      uploadError: null,
      currentStatus: null
    };
  },
  computed: {
    isInitial() {
      return this.currentStatus === STATUS_INITIAL;
    },
    isSaving() {
      return this.currentStatus === STATUS_SAVING;
    },
    isSuccess() {
      return this.currentStatus === STATUS_SUCCESS;
    },
    isFailed() {
      return this.currentStatus === STATUS_FAILED;
    }
  },
  methods: {
    reset() {
      // reset form to initial state
      this.currentStatus = STATUS_INITIAL;
      this.uploadedFiles = [];
      this.uploadError = null;
    },
    save(formData) {
      // upload data to the server
      this.currentStatus = STATUS_SAVING;

      this.upload(formData)
        .then(x => {
          this.uploadedFiles = [].concat(x);
          this.currentStatus = STATUS_SUCCESS;
        })
        .catch(err => {
          this.uploadError = err.response;
          this.currentStatus = STATUS_FAILED;
        });
    },
    filesChange(fieldName, fileList) {
      // handle file changes
      const formData = new FormData();

      if (!fileList.length) return;

      // append the files to FormData
      Array.from(Array(fileList.length).keys())
        .map(x => {
          formData.append(fieldName, fileList[x], fileList[x].name);
        });

      // save it
      this.save(formData);
    },
    async upload(formData) {
      let data = null;
      try {
        data = await axios.post(this.url, formData);
        this.currentStatus = STATUS_SUCCESS;
      } catch (error) {
        this.uploadError = error;
        this.currentStatus = STATUS_FAILED;
      }
      return data;
    }
  },
  mounted() {
    this.reset();
  },
};
</script>

<style>
.dropbox {
  outline: 2px dashed grey; /* the dash box */
  outline-offset: -10px;
  background: lightcyan;
  color: dimgray;
  padding: 10px 10px;
  min-height: 200px; /* minimum height */
  position: relative;
  cursor: pointer;
}

.input-file {
  opacity: 0; /* invisible but it's there! */
  width: 100%;
  height: 200px;
  position: absolute;
  cursor: pointer;
}

.dropbox:hover {
  background: lightblue; /* when mouse over to the drop zone, change color */
}

.dropbox p {
  font-size: 1.2em;
  text-align: center;
  padding: 50px 0;
}
</style>
