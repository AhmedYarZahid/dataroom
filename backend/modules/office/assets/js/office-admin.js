/*var markers = [
  { id: 1, name: "Paris", top: 5, left: 5 },
  { id: 2, name: "Very Long Marker", top: 20, left: 50 }
];*/

Vue.component('map-marker', {
  template: '#marker-template',
  
  props: ['marker'],

  data: function() {
    return {
      editedMarker: null,
      editing: false
    }
  },

  directives: {
    'marker-focus': function (el, binding) {
      if (binding.value) {
        el.focus()
      }
    }
  },

  mounted: function () {
    this.$nextTick(function () {
      var self = this;

      $(this.$el).draggable({
        containment: '#map',
        handle: '.marker',
        stop: this.dragStop
      });

      $(this.$el).find(".marker-label.draggable").draggable({
        containment: '#map',
        stop: this.dragStopLabel
      });
    })
  },

  methods: {
    editMarker: function () {
      this.beforeEditCache = this.marker.name;
      this.editing = true;
    },
    doneEdit: function () {
      this.editing = false;
      this.marker.name = this.marker.name.trim();

      if (!this.marker.name) {
        this.cancelEdit();
      }
    },
    cancelEdit: function (marker) {
      this.editing = false;
      this.marker.name = this.beforeEditCache;
    },

    dragStop: function(event, ui) {
      var pos = this.pxToPercent($(this.$el), ui.position.top, ui.position.left);
      this.marker.top = pos.top;
      this.marker.left = pos.left;
    },
    dragStopLabel: function(event, ui) {
      var pos = this.pxToPercent($(this.$el).find(".marker-label.draggable"), ui.position.top, ui.position.left);
      
      this.marker.labelTop = pos.top;
      this.marker.labelLeft = pos.left;
    },
    pxToPercent: function(el, posTop, posLeft) {
      var topPercent = posTop / el.parent().height() * 100;
      var leftPercent = posLeft / el.parent().width() * 100;

      return {top: topPercent, left: leftPercent};
    }
  }
})

var app = new Vue({
  el: '#app',
  props: ['url'],
  data: {
    markers: markers,
    newMarker: '',
    updating: false,
  },

  methods: {
    addMarker: function() {
      var value = this.newMarker && this.newMarker.trim();
      if (!value) {
        return;
      }

      this.markers.push({
        id: '_' + (this.markers.length + 1),
        name: value,
        top: 45,
        left: 45,
        labelTop: '',
        labelLeft: ''
      });
      this.newMarker = '';
    },

    submit: function () {
      if (this.updating) {
        return;
      }

      this.updating = true;
      var self = this;

      var data = {markers: this.markers};
      $.post($(this.$el).data('url'), data, function() {
        self.updating = false;
        location.reload();
      });
    }
  },
});