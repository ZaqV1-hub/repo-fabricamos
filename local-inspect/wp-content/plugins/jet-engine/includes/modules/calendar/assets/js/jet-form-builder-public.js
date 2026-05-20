/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./field-ui/advanced-date-field-render.js":
/*!************************************************!*\
  !*** ./field-ui/advanced-date-field-render.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _readOnlyError(r) { throw new TypeError('"' + r + '" is read-only'); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var JetEngineRenderAdvancedDateField = /*#__PURE__*/function () {
  function JetEngineRenderAdvancedDateField(selector, args) {
    _classCallCheck(this, JetEngineRenderAdvancedDateField);
    var labels = args.labels,
      templateEngine = args.templateEngine,
      initCallback = args.initCallback;
    this.initCallbacks = [];
    this.templateEngine = templateEngine || 'wp';
    if (initCallback) {
      this.addInitCallback(initCallback);
    }
    this.labels = _objectSpread(_objectSpread({}, {
      startDate: 'Start date',
      hasEndDate: 'Has end date',
      endTime: 'End time',
      isRecurring: 'Is recurring?',
      repeat: 'Repeat',
      every: 'Every',
      onDay: 'on day',
      onThe: 'on the',
      recurring: 'Recurring',
      recurringPeriod: 'Recurring period',
      weekDays: 'Week days',
      monthlyType: 'Monthly type',
      monthDay: 'Month day',
      monthDayType: 'Month day type',
      monthDayTypeValue: 'Month day type value',
      month: 'Month',
      end: 'End',
      after: 'After',
      onDate: 'On Date',
      iterations: 'iterations',
      confirmDelete: 'Are you sure?',
      confirmDeleteYes: 'Yes',
      confirmDeleteNo: 'No',
      addDate: '+ Add date',
      recurringLabelDaily: 'Daily',
      recurringLabelWeekly: 'Weekly',
      recurringLabelMonthly: 'Monthly',
      recurringLabelYearly: 'Yearly',
      recurringSubLabelDaily: 'day(s)',
      recurringSubLabelWeekly: 'week(s)',
      recurringSubLabelMonthly: 'month(s)',
      recurringSubLabelYearly: 'year(s)',
      first: 'First',
      second: 'Second',
      third: 'Third',
      fourth: 'Fourth',
      last: 'Last',
      day: 'Day',
      weekDayMon: 'Mon',
      weekDayTue: 'Tue',
      weekDayWed: 'Wed',
      weekDayThu: 'Thu',
      weekDayFri: 'Fri',
      weekDaySat: 'Sat',
      weekDaySun: 'Sun',
      monthJan: 'Jan',
      monthFeb: 'Feb',
      monthMar: 'Mar',
      monthApr: 'Apr',
      monthMay: 'May',
      monthJun: 'Jun',
      monthJul: 'Jul',
      monthAug: 'Aug',
      monthSep: 'Sep',
      monthOct: 'Oct',
      monthNov: 'Nov',
      monthDec: 'Dec'
    }), labels);
    this.setup(selector);
    this.render();
    this.events();
  }
  return _createClass(JetEngineRenderAdvancedDateField, [{
    key: "addInitCallback",
    value: function addInitCallback(callback) {
      this.initCallbacks.push(callback);
    }
  }, {
    key: "setup",
    value: function setup(selector) {
      this.$container = jQuery('<div></div>').appendTo(selector);
      this.$input = selector.find('input[type="hidden"]');
      this.fieldName = this.$input.attr('name');
      this.required = this.$input.attr('required') || false;
      this.value = this.$input.val() || '{}';
      this.allowTime = this.$input.data('allow-time');
      this.dataFormat = this.$input.data('format') || 'rrule';
      try {
        this.value = JSON.parse(this.value);
      } catch (e) {
        this.value = {};
      }
      this.dates = this.value.dates || [];
      this.date = this.value.date || '';
      this.time = this.value.time || '';
      this.isEndDate = this.value.is_end_date || false;
      this.endDate = this.value.end_date || '';
      this.endTime = this.value.end_time || '';
      this.isRecurring = this.value.is_recurring || false;
      this.recurring = this.value.recurring || 'weekly';
      this.recurringPeriod = this.value.recurring_period || 1;
      this.weekDays = this.value.week_days || [];
      this.monthlyType = this.value.monthly_type || 'on_day';
      this.monthDay = this.value.month_day || 1;
      this.monthDayType = this.value.month_day_type || 'first';
      this.monthDayTypeValue = this.value.month_day_type_value || 'Sun';
      this.month = this.value.month || 'Jan';
      this.end = this.value.end || 'after';
      this.endAfter = this.value.end_after || 5;
      this.endAfterDate = this.value.end_after_date || '';
      if (!this.dates.length) {
        this.dates = [];
        this.dates.push({
          date: this.date,
          time: this.time,
          isEndDate: this.isEndDate,
          endDate: this.endDate,
          endTime: this.endTime
        });
      }

      // fix saved switchers
      var switchers = ['isEndDate', 'isRecurring'];
      for (var i = 0; i < switchers.length; i++) {
        if (1 == this[switchers[i]]) {
          this[switchers[i]] = true;
        } else {
          this[switchers[i]] = false;
        }
      }
    }
  }, {
    key: "getProps",
    value: function getProps() {
      return {
        dates: this.dates,
        date: this.date,
        time: this.time,
        is_end_date: this.isEndDate,
        end_date: this.endDate,
        end_time: this.endTime,
        is_recurring: this.isRecurring,
        recurring: this.recurring,
        recurring_period: this.recurringPeriod,
        week_days: this.weekDays,
        monthly_type: this.monthlyType,
        month_day: this.monthDay,
        month_day_type: this.monthDayType,
        month_day_type_value: this.monthDayTypeValue,
        month: this.month,
        end: this.end,
        end_after: this.endAfter,
        end_after_date: this.endAfterDate,
        data_format: this.dataFormat
      };
    }
  }, {
    key: "render",
    value: function render() {
      if ('rrule' === this.dataFormat) {
        this.renderRecurring();
      } else {
        this.renderCustom();
      }
    }
  }, {
    key: "renderCustom",
    value: function renderCustom() {
      var _this = this;
      var repeaterWrapper = document.createElement('div');
      var repeaterNew = document.createElement('button');
      repeaterWrapper.classList.add('jet-engine-advanced-date-field__repeater');
      repeaterNew.classList.add('jet-engine-advanced-date-field__repeater-new');
      repeaterNew.setAttribute('type', 'button');
      repeaterNew.innerHTML = this.labels.addDate;
      for (var i = 0; i < this.dates.length; i++) {
        if (!this.dates[i].uid) {
          this.dates[i].uid = this.randomID();
        }
        repeaterWrapper.append(this.getNewRepeaterEl(this.formatData(this.dates[i])));
      }
      repeaterNew.addEventListener('click', function (event) {
        event.preventDefault();
        var newDate = {
          uid: _this.randomID(),
          date: '',
          time: '',
          isEndDate: false,
          endDate: '',
          endTime: ''
        };
        _this.dates.push(newDate);
        repeaterWrapper.append(_this.getNewRepeaterEl(newDate));
      });
      this.$container.append(repeaterWrapper);
      this.$container.append(repeaterNew);
    }
  }, {
    key: "formatData",
    value: function formatData(data) {
      var formatted = {};
      for (var key in data) {
        var newKey = key.replace(/_([a-z])/g, function (g) {
          return g[1].toUpperCase();
        });
        formatted[newKey] = data[key];
      }
      return formatted;
    }
  }, {
    key: "randomID",
    value: function randomID() {
      var min = 1000;
      var max = 9999;
      return Math.floor(Math.random() * (max - min + 1) + min);
    }
  }, {
    key: "getNewRepeaterEl",
    value: function getNewRepeaterEl(data) {
      var _this2 = this;
      if (!this.fieldTemplate) {
        if ('wp' === this.templateEngine) {
          this.fieldTemplate = wp.template('jet-engine-advanced-date-field-custom');
        } else {
          this.fieldTemplate = this.templateEngine('base-date');
        }
      }
      var defaults = {
        required: false,
        allowTime: this.allowTime,
        fieldName: this.fieldName + '[dates][' + data.uid + ']',
        labels: this.labels
      };
      data = _objectSpread(_objectSpread({}, defaults), data);
      var newRow = document.createElement('div');
      newRow.classList.add('jet-engine-advanced-date-field__repeater-row');
      newRow.innerHTML = this.fieldTemplate(data);
      newRow.dataset.index = data.uid;
      newRow.setAttribute('data-index', data.uid);
      var deleteButtonWrap = document.createElement('div');
      var deleteButton = document.createElement('button');
      var deleteButtonConfirm = document.createElement('div');
      deleteButtonWrap.classList.add('jet-engine-advanced-date-field__repeater-delete');
      deleteButton.classList.add('jet-engine-advanced-date-field__repeater-delete-button');
      deleteButton.setAttribute('type', 'button');
      deleteButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M12 4h3c.6 0 1 .4 1 1v1H3V5c0-.6.5-1 1-1h3c.2-1.1 1.3-2 2.5-2s2.3.9 2.5 2zM8 4h3c-.2-.6-.9-1-1.5-1S8.2 3.4 8 4zM4 7h11l-.9 10.1c0 .5-.5.9-1 .9H5.9c-.5 0-.9-.4-1-.9L4 7z"/></g></svg>';
      deleteButtonConfirm.classList.add('jet-engine-advanced-date-field__repeater-confirm');
      deleteButtonConfirm.innerHTML = "".concat(this.labels.confirmDelete, " <span class=\"jet-engine-advanced-date-field__repeater-confirm-yes\">").concat(this.labels.confirmDeleteYes, "</span><span class=\"jet-engine-advanced-date-field__repeater-confirm-no\">").concat(this.labels.confirmDeleteNo, "</span>");
      deleteButtonWrap.append(deleteButton);
      deleteButtonWrap.append(deleteButtonConfirm);
      deleteButton.addEventListener('click', function (event) {
        event.preventDefault();
        deleteButtonWrap.classList.add('show-confirm');
      });
      deleteButtonConfirm.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (event.target.classList.contains('jet-engine-advanced-date-field__repeater-confirm-yes')) {
          newRow.remove();
          _this2.dates = _this2.dates.filter(function (item) {
            return item.uid != data.uid;
          });
          _this2.$input.attr('value', JSON.stringify(_this2.getProps()));
        }
        if (event.target.classList.contains('jet-engine-advanced-date-field__repeater-confirm-no')) {
          deleteButtonWrap.classList.remove('show-confirm');
        }
      });
      newRow.append(deleteButtonWrap);
      this.initCallbacks.forEach(function (callback) {
        callback({
          instance: _this2,
          newRow: newRow
        });
      });
      return newRow;
    }
  }, {
    key: "renderRecurring",
    value: function renderRecurring() {
      var _this3 = this;
      var fieldTemplate;
      if ('wp' === this.templateEngine) {
        fieldTemplate = wp.template('jet-engine-advanced-date-field-rrule');
      } else {
        fieldTemplate = this.templateEngine();
      }
      var templateData = {
        labels: this.labels,
        weekdaysConfig: [{
          value: 1,
          label: this.labels.weekDayMon
        }, {
          value: 2,
          label: this.labels.weekDayTue
        }, {
          value: 3,
          label: this.labels.weekDayWed
        }, {
          value: 4,
          label: this.labels.weekDayThu
        }, {
          value: 5,
          label: this.labels.weekDayFri
        }, {
          value: 6,
          label: this.labels.weekDaySat
        }, {
          value: 7,
          label: this.labels.weekDaySun
        }],
        months: [{
          value: 1,
          label: this.labels.monthJan
        }, {
          value: 2,
          label: this.labels.monthFeb
        }, {
          value: 3,
          label: this.labels.monthMar
        }, {
          value: 4,
          label: this.labels.monthApr
        }, {
          value: 5,
          label: this.labels.monthMay
        }, {
          value: 6,
          label: this.labels.monthJun
        }, {
          value: 7,
          label: this.labels.monthJul
        }, {
          value: 8,
          label: this.labels.monthAug
        }, {
          value: 9,
          label: this.labels.monthSep
        }, {
          value: 10,
          label: this.labels.monthOct
        }, {
          value: 11,
          label: this.labels.monthNov
        }, {
          value: 12,
          label: this.labels.monthDec
        }],
        recurrings: [{
          value: 'daily',
          label: this.labels.recurringLabelDaily,
          sublabel: this.labels.recurringSubLabelDaily
        }, {
          value: 'weekly',
          label: this.labels.recurringLabelWeekly,
          sublabel: this.labels.recurringSubLabelWeekly
        }, {
          value: 'monthly',
          label: this.labels.recurringLabelMonthly,
          sublabel: this.labels.recurringSubLabelMonthly
        }, {
          value: 'yearly',
          label: this.labels.recurringLabelYearly,
          sublabel: this.labels.recurringSubLabelYearly
        }],
        required: this.required,
        fieldName: this.fieldName,
        date: this.date,
        time: this.time,
        isEndDate: this.isEndDate,
        endDate: this.endDate,
        endTime: this.endTime,
        isRecurring: this.isRecurring,
        recurring: this.recurring,
        recurringPeriod: this.recurringPeriod,
        weekDays: this.weekDays,
        monthlyType: this.monthlyType,
        monthDay: this.monthDay,
        monthDayType: this.monthDayType,
        monthDayTypeValue: this.monthDayTypeValue,
        month: this.month,
        end: this.end,
        endAfter: this.endAfter,
        endAfterDate: this.endAfterDate,
        allowTime: this.allowTime
      };
      this.$container.html(fieldTemplate(templateData));
      this.initCallbacks.forEach(function (callback) {
        callback({
          instance: _this3
        });
      });
    }
  }, {
    key: "selectors",
    value: function selectors(which) {
      var selectors = {
        date: 'input[name="' + this.fieldName + '[date]"]',
        time: 'input[name="' + this.fieldName + '[time]"]',
        isEndDate: 'input[name="' + this.fieldName + '[is_end_date]"]',
        endDate: 'input[name="' + this.fieldName + '[end_date]"]',
        endTime: 'select[name="' + this.fieldName + '[end_time]"]',
        isRecurring: 'input[name="' + this.fieldName + '[is_recurring]"]',
        recurring: 'select[name="' + this.fieldName + '[recurring]"]',
        recurringPeriod: 'input[name="' + this.fieldName + '[recurring_period]"]',
        weekDays: 'input[name="' + this.fieldName + '[week_days][]"]',
        monthlyType: 'input[name="' + this.fieldName + '[monthly_type]"]',
        monthDay: 'select[name="' + this.fieldName + '[month_day]"]',
        monthDayType: 'select[name="' + this.fieldName + '[month_day_type]"]',
        monthDayTypeValue: 'select[name="' + this.fieldName + '[month_day_type_value]"]',
        month: 'select[name="' + this.fieldName + '[month]"]',
        end: 'select[name="' + this.fieldName + '[end]"]',
        endAfter: 'input[name="' + this.fieldName + '[end_after]"]',
        endAfterDate: 'input[name="' + this.fieldName + '[end_after_date]"]'
      };
      if (which) {
        return selectors[which];
      } else {
        return selectors;
      }
    }
  }, {
    key: "update",
    value: function update(data, silent) {
      silent = silent || false;
      var updated = false;
      for (var key in data) {
        if (this[key] !== data[key]) {
          this[key] = data[key];
          updated = true;
        }
      }
      this.$input.attr('value', JSON.stringify(this.getProps()));
      if (!silent && updated) {
        this.render();
      }
    }
  }, {
    key: "events",
    value: function events() {
      if ('rrule' === this.dataFormat) {
        this.eventsRRule();
      } else {
        this.eventsCustom();
      }
    }
  }, {
    key: "eventsCustom",
    value: function eventsCustom() {
      var _this4 = this;
      this.$container.on('change', '.jet-engine-advanced-date-field--switcher, .field-type-switcher input', function (event) {
        var $row = jQuery(event.target).closest('.jet-engine-advanced-date-field__repeater-row');
        var index = $row.data('index');
        if ('isEndDate' === event.target.dataset.key) {
          for (var i = 0; i < _this4.dates.length; i++) {
            if (_this4.dates[i].uid == index) {
              if (undefined === _this4.dates[i].isEndDate) {
                _this4.dates[i].isEndDate = event.target.checked;
              } else {
                _this4.dates[i].isEndDate = !_this4.dates[i].isEndDate;
              }
              $row.replaceWith(_this4.getNewRepeaterEl(_this4.dates[i]));
              break;
            }
          }
        }
        _this4.$input.attr('value', JSON.stringify(_this4.getProps()));
      });
      jQuery(window).on('cx-control-change change', function (event) {
        var target = event.input || jQuery(event.target);
        if (target && target.hasClass('jet-engine-advanced-date-field--control')) {
          var $row = target.closest('.jet-engine-advanced-date-field__repeater-row');
          var index = $row.data('index');
          for (var i = 0; i < _this4.dates.length; i++) {
            if (_this4.dates[i].uid == index) {
              var value = event.controlStatus || event.target.value;
              _this4.dates[i][target.data('key')] = value;
              break;
            }
          }
          _this4.$input.attr('value', JSON.stringify(_this4.getProps()));
        }
      });
    }
  }, {
    key: "eventsRRule",
    value: function eventsRRule() {
      var _this5 = this;
      var switchers = ['isEndDate', 'isRecurring'];
      var regularEnvents = ['end', 'recurring', 'month'];
      var silentEvents = ['recurringPeriod', 'monthlyType', 'monthDay', 'monthDayType', 'monthDayTypeValue', 'endAfter'];
      var dates = {
        date: this.fieldName + '[date]',
        time: this.fieldName + '[time]',
        endDate: this.fieldName + '[end_date]',
        endTime: this.fieldName + '[end_time]',
        endAfterDate: this.fieldName + '[end_after_date]'
      };
      for (var i = 0; i < switchers.length; i++) {
        this.$container.on('change', this.selectors(switchers[i]), function (key, event) {
          _this5.update(_defineProperty({}, key, event.target.checked));
        }.bind(undefined, switchers[i]));
      }
      for (var i = 0; i < regularEnvents.length; i++) {
        this.$container.on('change', this.selectors(regularEnvents[i]), function (key, event) {
          _this5.update(_defineProperty({}, key, event.target.value));
        }.bind(undefined, regularEnvents[i]));
      }
      for (var i = 0; i < silentEvents.length; i++) {
        this.$container.on('change', this.selectors(silentEvents[i]), function (key, event) {
          _this5.update(_defineProperty({}, key, event.target.value), true);
        }.bind(undefined, silentEvents[i]));
      }
      for (var prop in dates) {
        // Fallback for Admin UI
        jQuery(window).on('cx-control-change', function (key, name, event) {
          if (event.controlName == name) {
            _this5.update(_defineProperty({}, key, event.controlStatus), true);
          }
        }.bind(undefined, prop, dates[prop]));
        this.$container.on('change', this.selectors(dates[prop]), function (key, name, event) {
          var _event$target;
          if ((event === null || event === void 0 || (_event$target = event.target) === null || _event$target === void 0 ? void 0 : _event$target.name) == name) {
            _this5.update(_defineProperty({}, key, event.target.value), true);
          }
        }.bind(undefined, prop, dates[prop]));
      }
      this.$container.on('change', this.selectors('weekDays'), function (event) {
        var newDays = [];
        var checked = document.querySelectorAll(_this5.selectors('weekDays') + ':checked');
        if (checked && checked.length) {
          for (var i = 0; i < checked.length; i++) {
            newDays.push(checked[i].value);
          }
        }
        _this5.update({
          weekDays: newDays
        });
      });
    }
  }]);
}();
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (JetEngineRenderAdvancedDateField);

/***/ }),

/***/ "./field-ui/template-factory.js":
/*!**************************************!*\
  !*** ./field-ui/template-factory.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _template__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./template */ "./field-ui/template.js");
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }

var createTemplateEngine = function createTemplateEngine() {
  var templateEngine = new _template__WEBPACK_IMPORTED_MODULE_0__["default"]();
  var templateEngineFunc = function templateEngineFunc() {};
  Object.setPrototypeOf(templateEngineFunc, templateEngine);
  return new Proxy(templateEngineFunc, {
    apply: function apply(target, thisArg, args) {
      var _args = _slicedToArray(args, 1),
        context = _args[0];
      switch (context) {
        case 'rrule':
          return templateEngine.getRecurringFieldTemplate.bind(templateEngine);
        case 'base-date':
          return templateEngine.getBaseDateFieldTemplate.bind(templateEngine);
        default:
          return templateEngine.render.bind(templateEngine);
      }
    }
  });
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (createTemplateEngine);

/***/ }),

/***/ "./field-ui/template.js":
/*!******************************!*\
  !*** ./field-ui/template.js ***!
  \******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/**
 * Advanced Date field template class.
 * Works in similar way as back bone template.
 * Class contains HTML markup with template literals.
 * From constructor receives dynamic data and renders tamplate accoding retrieved data.
 * @class
 */
var JetEngineAdvancedDateFieldTemplate = /*#__PURE__*/function () {
  function JetEngineAdvancedDateFieldTemplate() {
    _classCallCheck(this, JetEngineAdvancedDateFieldTemplate);
  }
  return _createClass(JetEngineAdvancedDateFieldTemplate, [{
    key: "render",
    value: function render(data) {
      return this.getTemplate(data);
    }

    /**
     * Get template
     *
     * @return {string}
     */
  }, {
    key: "getTemplate",
    value: function getTemplate(data) {
      return "\n\t\t\t".concat(this.getBaseDateFieldTemplate(data), "\n\t\t\t").concat(this.getRecurringFieldTemplate(data), "\n\t\t");
    }
  }, {
    key: "getBaseDateFieldTemplate",
    value: function getBaseDateFieldTemplate(data) {
      return "\n\t\t\t".concat(this.getStartDateFieldTemplate(data), "\n\t\t\t").concat(this.getEndDateFieldTemplate(data), "\n\t\t");
    }
  }, {
    key: "getStartDateFieldTemplate",
    value: function getStartDateFieldTemplate(data) {
      return "\n\t\t\t<div class=\"jet-engine-advanced-date-field__date\">\n\t\t\t\t<span class=\"jet-engine-advanced-date-field__label\">".concat(data.labels.startDate, "</span>\n\t\t\t\t<div class=\"jet-engine-advanced-date-field__date-warp ").concat(data.required ? 'is-required' : '', "\" data-control-name=\"").concat(data.fieldName, "[date]\">\n\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__date-controls\">\n\t\t\t\t\t\t<input\n\t\t\t\t\t\t\ttype=\"date\"\n\t\t\t\t\t\t\tclass=\"jet-engine-advanced-date-field__date-input jet-engine-advanced-date-field--control\"\n\t\t\t\t\t\t\tname=\"").concat(data.fieldName, "[date]\"\n\t\t\t\t\t\t\tplaceholder=\"Select date...\"\n\t\t\t\t\t\t\tvalue=\"").concat(data.date, "\"\n\t\t\t\t\t\t\tdata-key=\"date\"\n\t\t\t\t\t\t\t").concat(data.required ? 'required' : '', "\n\t\t\t\t\t\t>\n\t\t\t\t\t\t").concat(data.allowTime ? "\n\t\t\t\t\t\t\t<input\n\t\t\t\t\t\t\t\ttype=\"time\"\n\t\t\t\t\t\t\t\tclass=\"jet-engine-advanced-date-field__time-input jet-engine-advanced-date-field--control\"\n\t\t\t\t\t\t\t\tname=\"".concat(data.fieldName, "[time]\"\n\t\t\t\t\t\t\t\tplaceholder=\"Set time...\"\n\t\t\t\t\t\t\t\tvalue=\"").concat(data.time, "\"\n\t\t\t\t\t\t\t\tdata-key=\"time\"\n\t\t\t\t\t\t\t\t").concat(data.required ? 'required' : '', "\n\t\t\t\t\t\t\t>\n\t\t\t\t\t\t") : '', "\n\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"cx-control__error\"></div>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t");
    }
  }, {
    key: "getEndDateFieldTemplate",
    value: function getEndDateFieldTemplate(data) {
      return "\n\t\t\t<div class=\"jet-engine-advanced-date-field__date\">\n\t\t\t\t<span class=\"jet-engine-advanced-date-field__label\">".concat(data.labels.hasEndDate, "</span>\n\t\t\t\t<div class=\"jet-engine-advanced-date-field__end-date-controls\">\n\t\t\t\t\t<div class=\"field-type-switcher\">\n\t\t\t\t\t\t<input id=\"").concat(data.fieldName, "__is_end_date\" name=\"").concat(data.fieldName, "[is_end_date]\" type=\"checkbox\" role=\"switch\" class=\"jet-form-builder__field\" value=\"1\" ").concat(data.isEndDate ? 'checked' : '', " data-calculate=\"1\" data-jfb-sync=\"null\" data-key=\"isEndDate\">\n\t\t\t\t\t</div>\n\t\t\t\t\t").concat(data.isEndDate ? "\n\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__date-warp ".concat(data.required ? 'cx-control-required' : '', "\" data-control-name=\"").concat(data.fieldName, "[end_date]\">\n\t\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__date-controls\">\n\t\t\t\t\t\t\t\t<input\n\t\t\t\t\t\t\t\t\ttype=\"date\"\n\t\t\t\t\t\t\t\t\tclass=\"jet-engine-advanced-date-field__end-date-input jet-engine-advanced-date-field--control\"\n\t\t\t\t\t\t\t\t\tname=\"").concat(data.fieldName, "[end_date]\"\n\t\t\t\t\t\t\t\t\tplaceholder=\"Select date...\"\n\t\t\t\t\t\t\t\t\tvalue=\"").concat(data.endDate, "\"\n\t\t\t\t\t\t\t\t\tdata-key=\"endDate\"\n\t\t\t\t\t\t\t\t\t").concat(data.required ? 'required' : '', "\n\t\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t\t").concat(data.allowTime ? "\n\t\t\t\t\t\t\t\t\t<input\n\t\t\t\t\t\t\t\t\t\ttype=\"time\"\n\t\t\t\t\t\t\t\t\t\tclass=\"jet-engine-advanced-date-field__end-time-input jet-engine-advanced-date-field--control\"\n\t\t\t\t\t\t\t\t\t\tname=\"".concat(data.fieldName, "[end_time]\"\n\t\t\t\t\t\t\t\t\t\tplaceholder=\"Set time...\"\n\t\t\t\t\t\t\t\t\t\tvalue=\"").concat(data.endTime, "\"\n\t\t\t\t\t\t\t\t\t\tdata-key=\"endTime\"\n\t\t\t\t\t\t\t\t\t\t").concat(data.required ? 'required' : '', "\n\t\t\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t\t") : '', "\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"cx-control__error\"></div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t") : '', "\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t");
    }
  }, {
    key: "getRecurringFieldTemplate",
    value: function getRecurringFieldTemplate(data) {
      return "\n\t\t\t<div class=\"jet-engine-advanced-date-field__is-recurring\">\n\t\t\t\t<label class=\"jet-engine-advanced-date-field__label\" for=\"".concat(data.fieldName, "__is_recurring\">\n\t\t\t\t\t").concat(data.labels.isRecurring, "\n\t\t\t\t</label>\n\t\t\t\t<div class=\"field-type-switcher\">\n\t\t\t\t\t<input id=\"").concat(data.fieldName, "__is_recurring\" name=\"").concat(data.fieldName, "[is_recurring]\" type=\"checkbox\" role=\"switch\" class=\"jet-form-builder__field\" value=\"1\" ").concat(data.isRecurring ? 'checked' : '', " data-calculate=\"1\" data-jfb-sync=\"null\" data-key=\"isRecurring\">\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t\t").concat(data.isRecurring ? "\n\t\t\t\t<div class=\"jet-engine-advanced-date-field__recurring-wrap\">\n\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__recurring-row\">\n\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__recurring-label jet-engine-advanced-date-field__label\">\n\t\t\t\t\t\t\t".concat(data.labels.repeat, "\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__recurring-content\">\n\t\t\t\t\t\t\t<select name=\"").concat(data.fieldName, "[recurring]\" class=\"cx-ui-select\">\n\t\t\t\t\t\t\t\t").concat(data.recurrings.map(function (recurring) {
        return "\n\t\t\t\t\t\t\t\t\t<option\n\t\t\t\t\t\t\t\t\t\tvalue=\"".concat(recurring.value, "\"\n\t\t\t\t\t\t\t\t\t\t").concat(data.recurring === recurring.value ? 'selected' : '', "\n\t\t\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t\t\t\t").concat(recurring.label, "\n\t\t\t\t\t\t\t\t\t</option>\n\t\t\t\t\t\t\t\t");
      }).join(''), "\n\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__recurring-label\">\n\t\t\t\t\t\t\t\t").concat(data.labels.every, "\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<input\n\t\t\t\t\t\t\t\ttype=\"number\"\n\t\t\t\t\t\t\t\tname=\"").concat(data.fieldName, "[recurring_period]\"\n\t\t\t\t\t\t\t\tmin=\"1\"\n\t\t\t\t\t\t\t\tvalue=\"").concat(data.recurringPeriod, "\"\n\t\t\t\t\t\t\t\tclass=\"cx-ui-text\"\n\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t").concat(data.recurrings.map(function (recurring) {
        return data.recurring === recurring.value ? "\n\t\t\t\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__recurring-label\">\n\t\t\t\t\t\t\t\t\t\t".concat(recurring.sublabel, "\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t") : '';
      }).join(''), "\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t").concat(data.recurring !== 'daily' ? "\n\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__recurring-row\">\n\t\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__recurring-label jet-engine-advanced-date-field__label label-weekdays\">\n\t\t\t\t\t\t\t\t&nbsp;\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__recurring-content\">\n\t\t\t\t\t\t\t\t".concat(data.recurring === 'weekly' ? "\n\t\t\t\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__weekdays\">\n\t\t\t\t\t\t\t\t\t\t".concat(data.weekdaysConfig.map(function (day) {
        return "\n\t\t\t\t\t\t\t\t\t\t\t<label aria-label=\"".concat(day.label, "\">\n\t\t\t\t\t\t\t\t\t\t\t\t<input\n\t\t\t\t\t\t\t\t\t\t\t\t\ttype=\"checkbox\"\n\t\t\t\t\t\t\t\t\t\t\t\t\tvalue=\"").concat(day.value, "\"\n\t\t\t\t\t\t\t\t\t\t\t\t\tname=\"").concat(data.fieldName, "[week_days][]\"\n\t\t\t\t\t\t\t\t\t\t\t\t\t").concat(data.weekDays.includes('' + day.value) ? 'checked' : '', "\n\t\t\t\t\t\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"jet-engine-advanced-date-field__weekday-label\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t").concat(day.label, "\n\t\t\t\t\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"jet-engine-advanced-date-field__weekday-marker\"></span>\n\t\t\t\t\t\t\t\t\t\t\t</label>\n\t\t\t\t\t\t\t\t\t\t");
      }).join(''), "\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t") : '', "\n\t\t\t\t\t\t\t\t").concat(data.recurring === 'monthly' || data.recurring === 'yearly' ? "\n\t\t\t\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__monthly\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__monthly-row\">\n\t\t\t\t\t\t\t\t\t\t\t<label>\n\t\t\t\t\t\t\t\t\t\t\t\t<input\n\t\t\t\t\t\t\t\t\t\t\t\t\ttype=\"radio\"\n\t\t\t\t\t\t\t\t\t\t\t\t\tvalue=\"on_day\"\n\t\t\t\t\t\t\t\t\t\t\t\t\tname=\"".concat(data.fieldName, "[monthly_type]\"\n\t\t\t\t\t\t\t\t\t\t\t\t\t").concat(data.monthlyType === 'on_day' ? 'checked' : '', "\n\t\t\t\t\t\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t\t\t\t\t\t").concat(data.labels.onDay, "\n\t\t\t\t\t\t\t\t\t\t\t</label>\n\t\t\t\t\t\t\t\t\t\t\t").concat(data.recurring === 'yearly' ? "\n\t\t\t\t\t\t\t\t\t\t\t\t<select name=\"".concat(data.fieldName, "[month]\" class=\"cx-ui-select\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t").concat(data.months.map(function (month) {
        return "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tvalue=\"".concat(month.value, "\"\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t").concat(month.value == data.month ? 'selected' : '', "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t").concat(month.label, "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</option>\n\t\t\t\t\t\t\t\t\t\t\t\t\t");
      }).join(''), "\n\t\t\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t\t") : '', "\n\t\t\t\t\t\t\t\t\t\t\t<select name=\"").concat(data.fieldName, "[month_day]\" class=\"cx-ui-select\">\n\t\t\t\t\t\t\t\t\t\t\t\t").concat(Array.from({
        length: 31
      }, function (_, i) {
        return i + 1;
      }).map(function (i) {
        return "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option\n\t\t\t\t\t\t\t\t\t\t\t\t\t\tvalue=\"".concat(i, "\"\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t").concat(i == data.monthDay ? 'selected' : '', "\n\t\t\t\t\t\t\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t").concat(i, "\n\t\t\t\t\t\t\t\t\t\t\t\t\t</option>\n\t\t\t\t\t\t\t\t\t\t\t\t");
      }).join(''), "\n\t\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__monthly-row\">\n\t\t\t\t\t\t\t\t\t\t\t<label>\n\t\t\t\t\t\t\t\t\t\t\t\t<input\n\t\t\t\t\t\t\t\t\t\t\t\t\ttype=\"radio\"\n\t\t\t\t\t\t\t\t\t\t\t\t\tvalue=\"on_day_type\"\n\t\t\t\t\t\t\t\t\t\t\t\t\tname=\"").concat(data.fieldName, "[monthly_type]\"\n\t\t\t\t\t\t\t\t\t\t\t\t\t").concat(data.monthlyType === 'on_day_type' ? 'checked' : '', "\n\t\t\t\t\t\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t\t\t\t\t\t").concat(data.labels.onThe, "\n\t\t\t\t\t\t\t\t\t\t\t</label>\n\t\t\t\t\t\t\t\t\t\t\t<select name=\"").concat(data.fieldName, "[month_day_type]\" class=\"cx-ui-select\">\n\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"first\" ").concat(data.monthDayType === 'first' ? 'selected' : '', ">").concat(data.labels.first, "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"second\" ").concat(data.monthDayType === 'second' ? 'selected' : '', ">").concat(data.labels.second, "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"third\" ").concat(data.monthDayType === 'third' ? 'selected' : '', ">").concat(data.labels.third, "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"fourth\" ").concat(data.monthDayType === 'fourth' ? 'selected' : '', ">").concat(data.labels.fourth, "</option>\n\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"last\" ").concat(data.monthDayType === 'last' ? 'selected' : '', ">").concat(data.labels.last, "</option>\n\t\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t\t<select name=\"").concat(data.fieldName, "[month_day_type_value]\" class=\"cx-ui-select\">\n\t\t\t\t\t\t\t\t\t\t\t\t").concat(data.weekdaysConfig.map(function (day) {
        return "\n\t\t\t\t\t\t\t\t\t\t\t\t\t<option\n\t\t\t\t\t\t\t\t\t\t\t\t\t\tvalue=\"".concat(day.value, "\"\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t").concat(day.value == data.monthDayTypeValue ? 'selected' : '', "\n\t\t\t\t\t\t\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t").concat(day.label, "\n\t\t\t\t\t\t\t\t\t\t\t\t\t</option>\n\t\t\t\t\t\t\t\t\t\t\t\t");
      }).join(''), "\n\t\t\t\t\t\t\t\t\t\t\t\t<option value=\"day\" ").concat(data.monthDayTypeValue === 'day' ? 'selected' : '', ">").concat(data.labels.day, "</option>\n\t\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t\t").concat(data.recurring === 'yearly' ? "\n\t\t\t\t\t\t\t\t\t\t\t\t<select name=\"".concat(data.fieldName, "[month]\" class=\"cx-ui-select\">\n\t\t\t\t\t\t\t\t\t\t\t\t\t").concat(data.months.map(function (month) {
        return "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t<option\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tvalue=\"".concat(month.value, "\"\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t").concat(month.value == data.month ? 'selected' : '', "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t").concat(month.label, "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t</option>\n\t\t\t\t\t\t\t\t\t\t\t\t\t");
      }).join(''), "\n\t\t\t\t\t\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t\t\t\t\t") : '', "\n\t\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t") : '', "\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t") : '', "\n\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__recurring-row\">\n\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__recurring-label jet-engine-advanced-date-field__label\">\n\t\t\t\t\t\t\t").concat(data.labels.end, "\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__recurring-content\">\n\t\t\t\t\t\t\t<select name=\"").concat(data.fieldName, "[end]\" class=\"cx-ui-select\">\n\t\t\t\t\t\t\t\t<option value=\"after\" ").concat(data.end === 'after' ? 'selected' : '', ">").concat(data.labels.after, "</option>\n\t\t\t\t\t\t\t\t<option value=\"on_date\" ").concat(data.end === 'on_date' ? 'selected' : '', ">").concat(data.labels.onDate, "</option>\n\t\t\t\t\t\t\t</select>\n\t\t\t\t\t\t\t").concat(data.end === 'after' ? "\n\t\t\t\t\t\t\t\t<input\n\t\t\t\t\t\t\t\t\ttype=\"number\"\n\t\t\t\t\t\t\t\t\tname=\"".concat(data.fieldName, "[end_after]\"\n\t\t\t\t\t\t\t\t\tmin=\"2\"\n\t\t\t\t\t\t\t\t\tvalue=\"").concat(data.endAfter, "\"\n\t\t\t\t\t\t\t\t\tclass=\"cx-ui-text\"\n\t\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t\t<div class=\"jet-engine-advanced-date-field__recurring-label\">\n\t\t\t\t\t\t\t\t\t").concat(data.labels.iterations, "\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t") : '', "\n\t\t\t\t\t\t\t").concat(data.end === 'on_date' ? "\n\t\t\t\t\t\t\t\t<input\n\t\t\t\t\t\t\t\t\ttype=\"date\"\n\t\t\t\t\t\t\t\t\tclass=\"jet-engine-advanced-date-field__date-input\"\n\t\t\t\t\t\t\t\t\tname=\"".concat(data.fieldName, "[end_after_date]\"\n\t\t\t\t\t\t\t\t\tplaceholder=\"Select date...\"\n\t\t\t\t\t\t\t\t\tvalue=\"").concat(data.endAfterDate, "\"\n\t\t\t\t\t\t\t\t\trequired\n\t\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t") : '', "\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t") : '', "\n\t\t");
    }
  }]);
}();
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (JetEngineAdvancedDateFieldTemplate);

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!************************************!*\
  !*** ./jet-form-builder-public.js ***!
  \************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var field_ui_advanced_date_field_render__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! field-ui/advanced-date-field-render */ "./field-ui/advanced-date-field-render.js");
/* harmony import */ var field_ui_template_factory__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! field-ui/template-factory */ "./field-ui/template-factory.js");
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }


var addFilter = window.JetPlugins.hooks.addFilter;
var _JetFormBuilderAbstra = JetFormBuilderAbstract,
  InputData = _JetFormBuilderAbstra.InputData;
function JetEngineAdvancedDate() {
  InputData.call(this);
  this.isSupported = function (node) {
    return node.classList.contains('jet-form-builder-advanced-date__input');
  };
  this.addListeners = function () {
    var _this = this;
    var _this$nodes = _slicedToArray(this.nodes, 1),
      node = _this$nodes[0];
    node.addEventListener('blur', function (event) {
      _this.value.current = event.target.value;
    });
    jQuery(node).on('change', function (e, tinycolor) {
      var _tinycolor$toString;
      _this.value.current = (_tinycolor$toString = tinycolor === null || tinycolor === void 0 ? void 0 : tinycolor.toString()) !== null && _tinycolor$toString !== void 0 ? _tinycolor$toString : '';
    });
  };
  this.setNode = function (node) {
    InputData.prototype.setNode.call(this, node);
    this.$input = jQuery(node);
    this.listenFormPageChange = false;
    if (!this.listenFormPageChange) {
      jQuery(window).on('jet-form-builder/switch-page', function () {
        // this.initDateField();
      });
      this.listenFormPageChange = true;
    }
    this.initDateField();
  };
  this.initDateField = function () {
    var labels = {};
    if (this.$input.data('labels')) {
      labels = this.$input.data('labels');
    }
    new field_ui_advanced_date_field_render__WEBPACK_IMPORTED_MODULE_0__["default"](this.$input.closest('.jfb-advanced-date'), {
      labels: labels,
      templateEngine: (0,field_ui_template_factory__WEBPACK_IMPORTED_MODULE_1__["default"])()
    });
  };
  this.setValue = function (newValue) {
    newValue = newValue || null;
    this.calcValue = newValue;
    this.value.current = newValue;
  };
}
JetEngineAdvancedDate.prototype = Object.create(InputData.prototype);
addFilter('jet.fb.inputs', 'jet-form-builder/signature-field', function (inputs) {
  inputs = [JetEngineAdvancedDate].concat(_toConsumableArray(inputs));
  return inputs;
});
})();

/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiamV0LWZvcm0tYnVpbGRlci1wdWJsaWMuanMiLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBQUE7QUFFQTtBQUFBO0FBRUE7QUFFQTtBQUNBO0FBR0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUVBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFJQTtBQUNBO0FBQ0E7QUFFQTtBQUFBO0FBQUE7QUFBQTtBQUdBO0FBQ0E7QUFBQTtBQUFBO0FBQUE7QUFJQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUVBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBS0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUFBO0FBQUE7QUFBQTtBQUdBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQUE7QUFBQTtBQUFBO0FBSUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQUE7QUFBQTtBQUFBO0FBRUE7QUFFQTtBQUNBO0FBRUE7QUFFQTtBQUNBO0FBQ0E7QUFFQTtBQUVBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFFQTtBQUVBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFFQTtBQUVBO0FBQ0E7QUFFQTtBQUFBO0FBQUE7QUFBQTtBQUdBO0FBRUE7QUFDQTtBQUFBO0FBQUE7QUFDQTtBQUNBO0FBRUE7QUFFQTtBQUFBO0FBQUE7QUFBQTtBQUdBO0FBQ0E7QUFFQTtBQUNBO0FBQUE7QUFBQTtBQUFBO0FBRUE7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBRUE7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUVBO0FBRUE7QUFDQTtBQUVBO0FBRUE7QUFDQTtBQUVBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBRUE7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUFBO0FBQUE7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBRUE7QUFFQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFBQTtBQUFBO0FBQUE7QUFFQTtBQUVBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQUE7QUFBQTtBQUFBO0FBSUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFBQTtBQUFBO0FBQUE7QUFJQTtBQUVBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFFQTtBQUNBO0FBQ0E7QUFFQTtBQUFBO0FBQUE7QUFBQTtBQUdBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUFBO0FBQUE7QUFBQTtBQUVBO0FBRUE7QUFFQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUVBO0FBRUE7QUFFQTtBQUVBO0FBRUE7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBRUE7QUFBQTtBQUFBO0FBQUE7QUFFQTtBQUVBO0FBS0E7QUFNQTtBQVNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBRUE7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUFBO0FBQUE7QUFFQTtBQUVBO0FBQUE7QUFBQTtBQUlBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNqb0JBO0FBRUE7QUFFQTtBQUNBO0FBRUE7QUFFQTtBQUNBO0FBQ0E7QUFBQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUN6QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFOQTtBQVFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUdBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUpBO0FBQUE7QUFBQTtBQU1BO0FBSUE7QUFBQTtBQUFBO0FBQUE7QUFHQTtBQUlBO0FBQUE7QUFBQTtBQUFBO0FBR0E7QUE4QkE7QUFBQTtBQUFBO0FBQUE7QUFHQTtBQXFDQTtBQUFBO0FBQUE7QUFBQTtBQUdBO0FBaUJBO0FBS0E7QUFjQTtBQUtBO0FBWUE7QUFTQTtBQXFCQTtBQUtBO0FBTUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUtBO0FBdUJBO0FBS0E7QUFPQTtBQUtBO0FBK0NBO0FBQUE7QUFBQTtBQUlBOzs7Ozs7QUN6U0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7O0FDdkJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDUEE7Ozs7O0FDQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDTkE7QUFDQTtBQUVBO0FBSUE7QUFDQTtBQUdBO0FBRUE7QUFFQTtBQUNBO0FBQ0E7QUFFQTtBQUFBO0FBRUE7QUFBQTtBQUVBO0FBQ0E7QUFDQTtBQUVBO0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUVBO0FBRUE7QUFFQTtBQUVBO0FBQ0E7QUFDQTtBQUFBO0FBRUE7QUFDQTtBQUVBO0FBQ0E7QUFFQTtBQUVBO0FBRUE7QUFDQTtBQUNBO0FBRUE7QUFHQTtBQUNBO0FBQ0E7QUFFQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBRUE7QUFJQTtBQUVBO0FBQ0EiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9maWVsZC11aS9hZHZhbmNlZC1kYXRlLWZpZWxkLXJlbmRlci5qcyIsIndlYnBhY2s6Ly8vLi9maWVsZC11aS90ZW1wbGF0ZS1mYWN0b3J5LmpzIiwid2VicGFjazovLy8uL2ZpZWxkLXVpL3RlbXBsYXRlLmpzIiwid2VicGFjazovLy93ZWJwYWNrL2Jvb3RzdHJhcCIsIndlYnBhY2s6Ly8vd2VicGFjay9ydW50aW1lL2RlZmluZSBwcm9wZXJ0eSBnZXR0ZXJzIiwid2VicGFjazovLy93ZWJwYWNrL3J1bnRpbWUvaGFzT3duUHJvcGVydHkgc2hvcnRoYW5kIiwid2VicGFjazovLy93ZWJwYWNrL3J1bnRpbWUvbWFrZSBuYW1lc3BhY2Ugb2JqZWN0Iiwid2VicGFjazovLy8uL2pldC1mb3JtLWJ1aWxkZXItcHVibGljLmpzIl0sInNvdXJjZXNDb250ZW50IjpbImNsYXNzIEpldEVuZ2luZVJlbmRlckFkdmFuY2VkRGF0ZUZpZWxkIHtcblxuXHRjb25zdHJ1Y3Rvciggc2VsZWN0b3IsIGFyZ3MgKSB7XG5cblx0XHRjb25zdCB7XG5cdFx0XHRsYWJlbHMsXG5cdFx0XHR0ZW1wbGF0ZUVuZ2luZSxcblx0XHRcdGluaXRDYWxsYmFja1xuXHRcdH0gPSBhcmdzO1xuXG5cdFx0dGhpcy5pbml0Q2FsbGJhY2tzID0gW107XG5cdFx0dGhpcy50ZW1wbGF0ZUVuZ2luZSA9IHRlbXBsYXRlRW5naW5lIHx8ICd3cCc7XG5cblx0XHRpZiAoICBpbml0Q2FsbGJhY2sgKSB7XG5cdFx0XHR0aGlzLmFkZEluaXRDYWxsYmFjayggaW5pdENhbGxiYWNrICk7XG5cdFx0fVxuXG5cdFx0dGhpcy5sYWJlbHMgPSB7XG5cdFx0XHQuLi57XG5cdFx0XHRcdHN0YXJ0RGF0ZTogJ1N0YXJ0IGRhdGUnLFxuXHRcdFx0XHRoYXNFbmREYXRlOiAnSGFzIGVuZCBkYXRlJyxcblx0XHRcdFx0ZW5kVGltZTogJ0VuZCB0aW1lJyxcblx0XHRcdFx0aXNSZWN1cnJpbmc6ICdJcyByZWN1cnJpbmc/Jyxcblx0XHRcdFx0cmVwZWF0OiAnUmVwZWF0Jyxcblx0XHRcdFx0ZXZlcnk6ICdFdmVyeScsXG5cdFx0XHRcdG9uRGF5OiAnb24gZGF5Jyxcblx0XHRcdFx0b25UaGU6ICdvbiB0aGUnLFxuXHRcdFx0XHRyZWN1cnJpbmc6ICdSZWN1cnJpbmcnLFxuXHRcdFx0XHRyZWN1cnJpbmdQZXJpb2Q6ICdSZWN1cnJpbmcgcGVyaW9kJyxcblx0XHRcdFx0d2Vla0RheXM6ICdXZWVrIGRheXMnLFxuXHRcdFx0XHRtb250aGx5VHlwZTogJ01vbnRobHkgdHlwZScsXG5cdFx0XHRcdG1vbnRoRGF5OiAnTW9udGggZGF5Jyxcblx0XHRcdFx0bW9udGhEYXlUeXBlOiAnTW9udGggZGF5IHR5cGUnLFxuXHRcdFx0XHRtb250aERheVR5cGVWYWx1ZTogJ01vbnRoIGRheSB0eXBlIHZhbHVlJyxcblx0XHRcdFx0bW9udGg6ICdNb250aCcsXG5cdFx0XHRcdGVuZDogJ0VuZCcsXG5cdFx0XHRcdGFmdGVyOiAnQWZ0ZXInLFxuXHRcdFx0XHRvbkRhdGU6ICdPbiBEYXRlJyxcblx0XHRcdFx0aXRlcmF0aW9uczogJ2l0ZXJhdGlvbnMnLFxuXHRcdFx0XHRjb25maXJtRGVsZXRlOiAnQXJlIHlvdSBzdXJlPycsXG5cdFx0XHRcdGNvbmZpcm1EZWxldGVZZXM6ICdZZXMnLFxuXHRcdFx0XHRjb25maXJtRGVsZXRlTm86ICdObycsXG5cdFx0XHRcdGFkZERhdGU6ICcrIEFkZCBkYXRlJyxcblx0XHRcdFx0cmVjdXJyaW5nTGFiZWxEYWlseTogJ0RhaWx5Jyxcblx0XHRcdFx0cmVjdXJyaW5nTGFiZWxXZWVrbHk6ICdXZWVrbHknLFxuXHRcdFx0XHRyZWN1cnJpbmdMYWJlbE1vbnRobHk6ICdNb250aGx5Jyxcblx0XHRcdFx0cmVjdXJyaW5nTGFiZWxZZWFybHk6ICdZZWFybHknLFxuXHRcdFx0XHRyZWN1cnJpbmdTdWJMYWJlbERhaWx5OiAnZGF5KHMpJyxcblx0XHRcdFx0cmVjdXJyaW5nU3ViTGFiZWxXZWVrbHk6ICd3ZWVrKHMpJyxcblx0XHRcdFx0cmVjdXJyaW5nU3ViTGFiZWxNb250aGx5OiAnbW9udGgocyknLFxuXHRcdFx0XHRyZWN1cnJpbmdTdWJMYWJlbFllYXJseTogJ3llYXIocyknLFxuXHRcdFx0XHRmaXJzdDogJ0ZpcnN0Jyxcblx0XHRcdFx0c2Vjb25kOiAnU2Vjb25kJyxcblx0XHRcdFx0dGhpcmQ6ICdUaGlyZCcsXG5cdFx0XHRcdGZvdXJ0aDogJ0ZvdXJ0aCcsXG5cdFx0XHRcdGxhc3Q6ICdMYXN0Jyxcblx0XHRcdFx0ZGF5OiAnRGF5Jyxcblx0XHRcdFx0d2Vla0RheU1vbjogJ01vbicsXG5cdFx0XHRcdHdlZWtEYXlUdWU6ICdUdWUnLFxuXHRcdFx0XHR3ZWVrRGF5V2VkOiAnV2VkJyxcblx0XHRcdFx0d2Vla0RheVRodTogJ1RodScsXG5cdFx0XHRcdHdlZWtEYXlGcmk6ICdGcmknLFxuXHRcdFx0XHR3ZWVrRGF5U2F0OiAnU2F0Jyxcblx0XHRcdFx0d2Vla0RheVN1bjogJ1N1bicsXG5cdFx0XHRcdG1vbnRoSmFuOiAnSmFuJyxcblx0XHRcdFx0bW9udGhGZWI6ICdGZWInLFxuXHRcdFx0XHRtb250aE1hcjogJ01hcicsXG5cdFx0XHRcdG1vbnRoQXByOiAnQXByJyxcblx0XHRcdFx0bW9udGhNYXk6ICdNYXknLFxuXHRcdFx0XHRtb250aEp1bjogJ0p1bicsXG5cdFx0XHRcdG1vbnRoSnVsOiAnSnVsJyxcblx0XHRcdFx0bW9udGhBdWc6ICdBdWcnLFxuXHRcdFx0XHRtb250aFNlcDogJ1NlcCcsXG5cdFx0XHRcdG1vbnRoT2N0OiAnT2N0Jyxcblx0XHRcdFx0bW9udGhOb3Y6ICdOb3YnLFxuXHRcdFx0XHRtb250aERlYzogJ0RlYycsXG5cdFx0XHR9LFxuXHRcdFx0Li4ubGFiZWxzXG5cdFx0fTtcblxuXHRcdHRoaXMuc2V0dXAoIHNlbGVjdG9yICk7XG5cdFx0dGhpcy5yZW5kZXIoKTtcblx0XHR0aGlzLmV2ZW50cygpO1xuXG5cdH1cblxuXHRhZGRJbml0Q2FsbGJhY2soIGNhbGxiYWNrICkge1xuXHRcdHRoaXMuaW5pdENhbGxiYWNrcy5wdXNoKCBjYWxsYmFjayApO1xuXHR9XG5cblx0c2V0dXAoIHNlbGVjdG9yICkge1xuXG5cdFx0dGhpcy4kY29udGFpbmVyID0galF1ZXJ5KCAnPGRpdj48L2Rpdj4nICkuYXBwZW5kVG8oIHNlbGVjdG9yICk7XG5cdFx0dGhpcy4kaW5wdXQgPSBzZWxlY3Rvci5maW5kKCAnaW5wdXRbdHlwZT1cImhpZGRlblwiXScgKTtcblx0XHR0aGlzLmZpZWxkTmFtZSA9IHRoaXMuJGlucHV0LmF0dHIoICduYW1lJyApO1xuXHRcdHRoaXMucmVxdWlyZWQgPSB0aGlzLiRpbnB1dC5hdHRyKCAncmVxdWlyZWQnICkgfHwgZmFsc2U7XG5cdFx0dGhpcy52YWx1ZSA9IHRoaXMuJGlucHV0LnZhbCgpIHx8ICd7fSc7XG5cdFx0dGhpcy5hbGxvd1RpbWUgPSB0aGlzLiRpbnB1dC5kYXRhKCAnYWxsb3ctdGltZScgKTtcblx0XHR0aGlzLmRhdGFGb3JtYXQgPSB0aGlzLiRpbnB1dC5kYXRhKCAnZm9ybWF0JyApIHx8ICdycnVsZSc7XG5cblx0XHR0cnkge1xuXHRcdFx0dGhpcy52YWx1ZSA9IEpTT04ucGFyc2UoIHRoaXMudmFsdWUgKTtcblx0XHR9IGNhdGNoKCBlICkge1xuXHRcdFx0dGhpcy52YWx1ZSA9IHt9O1xuXHRcdH1cblxuXHRcdHRoaXMuZGF0ZXMgPSB0aGlzLnZhbHVlLmRhdGVzIHx8IFtdO1xuXHRcdHRoaXMuZGF0ZSA9IHRoaXMudmFsdWUuZGF0ZSB8fCAnJztcblx0XHR0aGlzLnRpbWUgPSB0aGlzLnZhbHVlLnRpbWUgfHwgJyc7XG5cdFx0dGhpcy5pc0VuZERhdGUgPSB0aGlzLnZhbHVlLmlzX2VuZF9kYXRlIHx8IGZhbHNlO1xuXHRcdHRoaXMuZW5kRGF0ZSA9IHRoaXMudmFsdWUuZW5kX2RhdGUgfHwgJyc7XG5cdFx0dGhpcy5lbmRUaW1lID0gdGhpcy52YWx1ZS5lbmRfdGltZSB8fCAnJztcblx0XHR0aGlzLmlzUmVjdXJyaW5nID0gdGhpcy52YWx1ZS5pc19yZWN1cnJpbmcgfHwgZmFsc2U7XG5cdFx0dGhpcy5yZWN1cnJpbmcgPSB0aGlzLnZhbHVlLnJlY3VycmluZyB8fCAnd2Vla2x5Jztcblx0XHR0aGlzLnJlY3VycmluZ1BlcmlvZCA9IHRoaXMudmFsdWUucmVjdXJyaW5nX3BlcmlvZCB8fCAxO1xuXHRcdHRoaXMud2Vla0RheXMgPSB0aGlzLnZhbHVlLndlZWtfZGF5cyB8fCBbXTtcblx0XHR0aGlzLm1vbnRobHlUeXBlID0gdGhpcy52YWx1ZS5tb250aGx5X3R5cGUgfHwgJ29uX2RheSc7XG5cdFx0dGhpcy5tb250aERheSA9IHRoaXMudmFsdWUubW9udGhfZGF5IHx8IDE7XG5cdFx0dGhpcy5tb250aERheVR5cGUgPSB0aGlzLnZhbHVlLm1vbnRoX2RheV90eXBlIHx8ICdmaXJzdCc7XG5cdFx0dGhpcy5tb250aERheVR5cGVWYWx1ZSA9IHRoaXMudmFsdWUubW9udGhfZGF5X3R5cGVfdmFsdWUgfHwgJ1N1bic7XG5cdFx0dGhpcy5tb250aCA9IHRoaXMudmFsdWUubW9udGggfHwgJ0phbic7XG5cdFx0dGhpcy5lbmQgPSB0aGlzLnZhbHVlLmVuZCB8fCAnYWZ0ZXInO1xuXHRcdHRoaXMuZW5kQWZ0ZXIgPSB0aGlzLnZhbHVlLmVuZF9hZnRlciB8fCA1O1xuXHRcdHRoaXMuZW5kQWZ0ZXJEYXRlID0gdGhpcy52YWx1ZS5lbmRfYWZ0ZXJfZGF0ZSB8fCAnJztcblxuXHRcdGlmICggISB0aGlzLmRhdGVzLmxlbmd0aCApIHtcblxuXHRcdFx0dGhpcy5kYXRlcyA9IFtdO1xuXG5cdFx0XHR0aGlzLmRhdGVzLnB1c2goIHtcblx0XHRcdFx0ZGF0ZTogdGhpcy5kYXRlLFxuXHRcdFx0XHR0aW1lOiB0aGlzLnRpbWUsXG5cdFx0XHRcdGlzRW5kRGF0ZTogdGhpcy5pc0VuZERhdGUsXG5cdFx0XHRcdGVuZERhdGU6IHRoaXMuZW5kRGF0ZSxcblx0XHRcdFx0ZW5kVGltZTogdGhpcy5lbmRUaW1lXG5cdFx0XHR9ICk7XG5cdFx0fVxuXG5cdFx0Ly8gZml4IHNhdmVkIHN3aXRjaGVyc1xuXHRcdGNvbnN0IHN3aXRjaGVycyA9IFtcblx0XHRcdCdpc0VuZERhdGUnLFxuXHRcdFx0J2lzUmVjdXJyaW5nJ1xuXHRcdF07XG5cblx0XHRmb3IgKCB2YXIgaSA9IDA7IGkgPCBzd2l0Y2hlcnMubGVuZ3RoOyBpKysgKSB7XG5cdFx0XHRpZiAoIDEgPT0gdGhpc1sgc3dpdGNoZXJzWyBpIF0gXSApIHtcblx0XHRcdFx0dGhpc1sgc3dpdGNoZXJzWyBpIF0gXSA9IHRydWU7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHR0aGlzWyBzd2l0Y2hlcnNbIGkgXSBdID0gZmFsc2U7XG5cdFx0XHR9XG5cdFx0fVxuXG5cdH1cblxuXHRnZXRQcm9wcygpIHtcblx0XHRyZXR1cm4ge1xuXHRcdFx0ZGF0ZXM6IHRoaXMuZGF0ZXMsXG5cdFx0XHRkYXRlOiB0aGlzLmRhdGUsXG5cdFx0XHR0aW1lOiB0aGlzLnRpbWUsXG5cdFx0XHRpc19lbmRfZGF0ZTogdGhpcy5pc0VuZERhdGUsXG5cdFx0XHRlbmRfZGF0ZTogdGhpcy5lbmREYXRlLFxuXHRcdFx0ZW5kX3RpbWU6IHRoaXMuZW5kVGltZSxcblx0XHRcdGlzX3JlY3VycmluZzogdGhpcy5pc1JlY3VycmluZyxcblx0XHRcdHJlY3VycmluZzogdGhpcy5yZWN1cnJpbmcsXG5cdFx0XHRyZWN1cnJpbmdfcGVyaW9kOiB0aGlzLnJlY3VycmluZ1BlcmlvZCxcblx0XHRcdHdlZWtfZGF5czogdGhpcy53ZWVrRGF5cyxcblx0XHRcdG1vbnRobHlfdHlwZTogdGhpcy5tb250aGx5VHlwZSxcblx0XHRcdG1vbnRoX2RheTogdGhpcy5tb250aERheSxcblx0XHRcdG1vbnRoX2RheV90eXBlOiB0aGlzLm1vbnRoRGF5VHlwZSxcblx0XHRcdG1vbnRoX2RheV90eXBlX3ZhbHVlOiB0aGlzLm1vbnRoRGF5VHlwZVZhbHVlLFxuXHRcdFx0bW9udGg6IHRoaXMubW9udGgsXG5cdFx0XHRlbmQ6IHRoaXMuZW5kLFxuXHRcdFx0ZW5kX2FmdGVyOiB0aGlzLmVuZEFmdGVyLFxuXHRcdFx0ZW5kX2FmdGVyX2RhdGU6IHRoaXMuZW5kQWZ0ZXJEYXRlLFxuXHRcdFx0ZGF0YV9mb3JtYXQ6IHRoaXMuZGF0YUZvcm1hdCxcblx0XHR9XG5cdH1cblxuXHRyZW5kZXIoKSB7XG5cblx0XHRpZiAoICdycnVsZScgPT09IHRoaXMuZGF0YUZvcm1hdCApIHtcblx0XHRcdHRoaXMucmVuZGVyUmVjdXJyaW5nKCk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdHRoaXMucmVuZGVyQ3VzdG9tKCk7XG5cdFx0fVxuXG5cdH1cblxuXHRyZW5kZXJDdXN0b20oKSB7XG5cblx0XHRjb25zdCByZXBlYXRlcldyYXBwZXIgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCAnZGl2JyApO1xuXHRcdGNvbnN0IHJlcGVhdGVyTmV3ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCggJ2J1dHRvbicgKTtcblxuXHRcdHJlcGVhdGVyV3JhcHBlci5jbGFzc0xpc3QuYWRkKCAnamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19yZXBlYXRlcicgKTtcblxuXHRcdHJlcGVhdGVyTmV3LmNsYXNzTGlzdC5hZGQoICdqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX3JlcGVhdGVyLW5ldycgKTtcblx0XHRyZXBlYXRlck5ldy5zZXRBdHRyaWJ1dGUoICd0eXBlJywgJ2J1dHRvbicgKTtcblx0XHRyZXBlYXRlck5ldy5pbm5lckhUTUwgPSB0aGlzLmxhYmVscy5hZGREYXRlO1xuXG5cdFx0Zm9yICggdmFyIGkgPSAwOyBpIDwgdGhpcy5kYXRlcy5sZW5ndGg7IGkrKyApIHtcblxuXHRcdFx0aWYgKCAhIHRoaXMuZGF0ZXNbIGkgXS51aWQgKSB7XG5cdFx0XHRcdHRoaXMuZGF0ZXNbIGkgXS51aWQgPSB0aGlzLnJhbmRvbUlEKCk7XG5cdFx0XHR9XG5cblx0XHRcdHJlcGVhdGVyV3JhcHBlci5hcHBlbmQoIHRoaXMuZ2V0TmV3UmVwZWF0ZXJFbCggdGhpcy5mb3JtYXREYXRhKCB0aGlzLmRhdGVzWyBpIF0gKSApICk7XG5cdFx0fVxuXG5cdFx0cmVwZWF0ZXJOZXcuYWRkRXZlbnRMaXN0ZW5lciggJ2NsaWNrJywgKCBldmVudCApID0+IHtcblxuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuXHRcdFx0bGV0IG5ld0RhdGUgPSB7XG5cdFx0XHRcdHVpZDogdGhpcy5yYW5kb21JRCgpLFxuXHRcdFx0XHRkYXRlOiAnJyxcblx0XHRcdFx0dGltZTogJycsXG5cdFx0XHRcdGlzRW5kRGF0ZTogZmFsc2UsXG5cdFx0XHRcdGVuZERhdGU6ICcnLFxuXHRcdFx0XHRlbmRUaW1lOiAnJyxcblx0XHRcdH07XG5cblx0XHRcdHRoaXMuZGF0ZXMucHVzaCggbmV3RGF0ZSApO1xuXHRcdFx0cmVwZWF0ZXJXcmFwcGVyLmFwcGVuZCggdGhpcy5nZXROZXdSZXBlYXRlckVsKCBuZXdEYXRlICkgKTtcblxuXHRcdH0pO1xuXG5cdFx0dGhpcy4kY29udGFpbmVyLmFwcGVuZCggcmVwZWF0ZXJXcmFwcGVyICk7XG5cdFx0dGhpcy4kY29udGFpbmVyLmFwcGVuZCggcmVwZWF0ZXJOZXcgKTtcblxuXHR9XG5cblx0Zm9ybWF0RGF0YSggZGF0YSApIHtcblx0XHRjb25zdCBmb3JtYXR0ZWQgPSB7fTtcblxuXHRcdGZvciAoIGNvbnN0IGtleSBpbiBkYXRhICkge1xuXHRcdFx0bGV0IG5ld0tleSA9IGtleS5yZXBsYWNlKCAvXyhbYS16XSkvZywgKCBnICkgPT4geyByZXR1cm4gZ1sxXS50b1VwcGVyQ2FzZSgpOyB9IClcblx0XHRcdGZvcm1hdHRlZFsgbmV3S2V5IF0gPSBkYXRhWyBrZXkgXTtcblx0XHR9XG5cblx0XHRyZXR1cm4gZm9ybWF0dGVkO1xuXG5cdH1cblxuXHRyYW5kb21JRCgpIHtcblx0XHRjb25zdCBtaW4gPSAxMDAwO1xuXHRcdGNvbnN0IG1heCA9IDk5OTk7XG5cblx0XHRyZXR1cm4gTWF0aC5mbG9vciggTWF0aC5yYW5kb20oKSAqICggbWF4IC0gbWluICsgMSApICsgbWluICk7XG5cdH1cblxuXHRnZXROZXdSZXBlYXRlckVsKCBkYXRhICkge1xuXG5cdFx0aWYgKCAhIHRoaXMuZmllbGRUZW1wbGF0ZSApIHtcblx0XHRcdGlmICggJ3dwJyA9PT0gdGhpcy50ZW1wbGF0ZUVuZ2luZSApIHtcblx0XHRcdFx0dGhpcy5maWVsZFRlbXBsYXRlID0gd3AudGVtcGxhdGUoICdqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGQtY3VzdG9tJyApO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0dGhpcy5maWVsZFRlbXBsYXRlID0gdGhpcy50ZW1wbGF0ZUVuZ2luZSggJ2Jhc2UtZGF0ZScgKTtcblx0XHRcdH1cblx0XHR9XG5cblx0XHRjb25zdCBkZWZhdWx0cyA9IHtcblx0XHRcdHJlcXVpcmVkOiBmYWxzZSxcblx0XHRcdGFsbG93VGltZTogdGhpcy5hbGxvd1RpbWUsXG5cdFx0XHRmaWVsZE5hbWU6IHRoaXMuZmllbGROYW1lICsgJ1tkYXRlc11bJyArIGRhdGEudWlkICsgJ10nLFxuXHRcdFx0bGFiZWxzOiB0aGlzLmxhYmVscyxcblx0XHR9O1xuXG5cdFx0ZGF0YSA9IHsgLi4uZGVmYXVsdHMsIC4uLmRhdGEgfTtcblxuXHRcdGNvbnN0IG5ld1JvdyA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoICdkaXYnICk7XG5cblx0XHRuZXdSb3cuY2xhc3NMaXN0LmFkZCggJ2pldC1lbmdpbmUtYWR2YW5jZWQtZGF0ZS1maWVsZF9fcmVwZWF0ZXItcm93JyApO1xuXHRcdG5ld1Jvdy5pbm5lckhUTUwgPSB0aGlzLmZpZWxkVGVtcGxhdGUoIGRhdGEgKTtcblx0XHRuZXdSb3cuZGF0YXNldC5pbmRleCA9IGRhdGEudWlkO1xuXHRcdG5ld1Jvdy5zZXRBdHRyaWJ1dGUoICdkYXRhLWluZGV4JywgZGF0YS51aWQgKTtcblxuXHRcdGNvbnN0IGRlbGV0ZUJ1dHRvbldyYXAgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCAnZGl2JyApO1xuXHRcdGNvbnN0IGRlbGV0ZUJ1dHRvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoICdidXR0b24nICk7XG5cdFx0Y29uc3QgZGVsZXRlQnV0dG9uQ29uZmlybSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoICdkaXYnICk7XG5cblx0XHRkZWxldGVCdXR0b25XcmFwLmNsYXNzTGlzdC5hZGQoICdqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX3JlcGVhdGVyLWRlbGV0ZScgKTtcblxuXHRcdGRlbGV0ZUJ1dHRvbi5jbGFzc0xpc3QuYWRkKCAnamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19yZXBlYXRlci1kZWxldGUtYnV0dG9uJyApO1xuXHRcdGRlbGV0ZUJ1dHRvbi5zZXRBdHRyaWJ1dGUoICd0eXBlJywgJ2J1dHRvbicgKTtcblxuXHRcdGRlbGV0ZUJ1dHRvbi5pbm5lckhUTUwgPSAnPHN2ZyB4bWxucz1cImh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnXCIgd2lkdGg9XCIyMFwiIGhlaWdodD1cIjIwXCIgdmlld0JveD1cIjAgMCAyMCAyMFwiPjxyZWN0IHg9XCIwXCIgZmlsbD1cIm5vbmVcIiB3aWR0aD1cIjIwXCIgaGVpZ2h0PVwiMjBcIi8+PGc+PHBhdGggZD1cIk0xMiA0aDNjLjYgMCAxIC40IDEgMXYxSDNWNWMwLS42LjUtMSAxLTFoM2MuMi0xLjEgMS4zLTIgMi41LTJzMi4zLjkgMi41IDJ6TTggNGgzYy0uMi0uNi0uOS0xLTEuNS0xUzguMiAzLjQgOCA0ek00IDdoMTFsLS45IDEwLjFjMCAuNS0uNS45LTEgLjlINS45Yy0uNSAwLS45LS40LTEtLjlMNCA3elwiLz48L2c+PC9zdmc+JztcblxuXHRcdGRlbGV0ZUJ1dHRvbkNvbmZpcm0uY2xhc3NMaXN0LmFkZCggJ2pldC1lbmdpbmUtYWR2YW5jZWQtZGF0ZS1maWVsZF9fcmVwZWF0ZXItY29uZmlybScgKTtcblx0XHRkZWxldGVCdXR0b25Db25maXJtLmlubmVySFRNTCA9IGAke3RoaXMubGFiZWxzLmNvbmZpcm1EZWxldGV9IDxzcGFuIGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19yZXBlYXRlci1jb25maXJtLXllc1wiPiR7dGhpcy5sYWJlbHMuY29uZmlybURlbGV0ZVllc308L3NwYW4+PHNwYW4gY2xhc3M9XCJqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX3JlcGVhdGVyLWNvbmZpcm0tbm9cIj4ke3RoaXMubGFiZWxzLmNvbmZpcm1EZWxldGVOb308L3NwYW4+YDtcblxuXHRcdGRlbGV0ZUJ1dHRvbldyYXAuYXBwZW5kKCBkZWxldGVCdXR0b24gKTtcblx0XHRkZWxldGVCdXR0b25XcmFwLmFwcGVuZCggZGVsZXRlQnV0dG9uQ29uZmlybSApO1xuXG5cdFx0ZGVsZXRlQnV0dG9uLmFkZEV2ZW50TGlzdGVuZXIoICdjbGljaycsICggZXZlbnQgKSA9PiB7XG5cdFx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0ZGVsZXRlQnV0dG9uV3JhcC5jbGFzc0xpc3QuYWRkKCAnc2hvdy1jb25maXJtJyApO1xuXHRcdH0gKTtcblxuXHRcdGRlbGV0ZUJ1dHRvbkNvbmZpcm0uYWRkRXZlbnRMaXN0ZW5lciggJ2NsaWNrJywgKCBldmVudCApID0+IHtcblxuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXG5cdFx0XHRpZiAoIGV2ZW50LnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoICdqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX3JlcGVhdGVyLWNvbmZpcm0teWVzJyApICkge1xuXHRcdFx0XHRuZXdSb3cucmVtb3ZlKCk7XG5cdFx0XHRcdHRoaXMuZGF0ZXMgPSB0aGlzLmRhdGVzLmZpbHRlciggKCBpdGVtICkgPT4geyByZXR1cm4gaXRlbS51aWQgIT0gZGF0YS51aWQgfSApO1xuXHRcdFx0XHR0aGlzLiRpbnB1dC5hdHRyKCAndmFsdWUnLCBKU09OLnN0cmluZ2lmeSggdGhpcy5nZXRQcm9wcygpICkgKTtcblx0XHRcdH1cblxuXHRcdFx0aWYgKCBldmVudC50YXJnZXQuY2xhc3NMaXN0LmNvbnRhaW5zKCAnamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19yZXBlYXRlci1jb25maXJtLW5vJyApICkge1xuXHRcdFx0XHRkZWxldGVCdXR0b25XcmFwLmNsYXNzTGlzdC5yZW1vdmUoICdzaG93LWNvbmZpcm0nICk7XG5cdFx0XHR9XG5cblx0XHR9ICk7XG5cblx0XHRuZXdSb3cuYXBwZW5kKCBkZWxldGVCdXR0b25XcmFwICk7XG5cblx0XHR0aGlzLmluaXRDYWxsYmFja3MuZm9yRWFjaCggKCBjYWxsYmFjayApID0+IHtcblx0XHRcdGNhbGxiYWNrKCB7XG5cdFx0XHRcdGluc3RhbmNlOiB0aGlzLFxuXHRcdFx0XHRuZXdSb3c6IG5ld1Jvdyxcblx0XHRcdH0gKTtcblx0XHR9ICk7XG5cblx0XHRyZXR1cm4gbmV3Um93O1xuXHR9XG5cblx0cmVuZGVyUmVjdXJyaW5nKCkge1xuXG5cdFx0bGV0IGZpZWxkVGVtcGxhdGU7XG5cblx0XHRpZiAoICd3cCcgPT09IHRoaXMudGVtcGxhdGVFbmdpbmUgKSB7XG5cdFx0XHRmaWVsZFRlbXBsYXRlID0gd3AudGVtcGxhdGUoICdqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGQtcnJ1bGUnICk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdGZpZWxkVGVtcGxhdGUgPSB0aGlzLnRlbXBsYXRlRW5naW5lKCk7XG5cdFx0fVxuXG5cdFx0Y29uc3QgdGVtcGxhdGVEYXRhID0ge1xuXHRcdFx0bGFiZWxzOiB0aGlzLmxhYmVscyxcblx0XHRcdHdlZWtkYXlzQ29uZmlnOiBbIHtcblx0XHRcdFx0dmFsdWU6IDEsXG5cdFx0XHRcdGxhYmVsOiB0aGlzLmxhYmVscy53ZWVrRGF5TW9uLFxuXHRcdFx0fSwge1xuXHRcdFx0XHR2YWx1ZTogMixcblx0XHRcdFx0bGFiZWw6IHRoaXMubGFiZWxzLndlZWtEYXlUdWUsXG5cdFx0XHR9LCB7XG5cdFx0XHRcdHZhbHVlOiAzLFxuXHRcdFx0XHRsYWJlbDogdGhpcy5sYWJlbHMud2Vla0RheVdlZCxcblx0XHRcdH0sIHtcblx0XHRcdFx0dmFsdWU6IDQsXG5cdFx0XHRcdGxhYmVsOiB0aGlzLmxhYmVscy53ZWVrRGF5VGh1LFxuXHRcdFx0fSwge1xuXHRcdFx0XHR2YWx1ZTogNSxcblx0XHRcdFx0bGFiZWw6IHRoaXMubGFiZWxzLndlZWtEYXlGcmksXG5cdFx0XHR9LCB7XG5cdFx0XHRcdHZhbHVlOiA2LFxuXHRcdFx0XHRsYWJlbDogdGhpcy5sYWJlbHMud2Vla0RheVNhdCxcblx0XHRcdH0se1xuXHRcdFx0XHR2YWx1ZTogNyxcblx0XHRcdFx0bGFiZWw6IHRoaXMubGFiZWxzLndlZWtEYXlTdW4sXG5cdFx0XHR9IF0sXG5cdFx0XHRtb250aHM6IFsge1xuXHRcdFx0XHR2YWx1ZTogMSxcblx0XHRcdFx0bGFiZWw6IHRoaXMubGFiZWxzLm1vbnRoSmFuLFxuXHRcdFx0fSwge1xuXHRcdFx0XHR2YWx1ZTogMixcblx0XHRcdFx0bGFiZWw6IHRoaXMubGFiZWxzLm1vbnRoRmViLFxuXHRcdFx0fSwge1xuXHRcdFx0XHR2YWx1ZTogMyxcblx0XHRcdFx0bGFiZWw6IHRoaXMubGFiZWxzLm1vbnRoTWFyLFxuXHRcdFx0fSwge1xuXHRcdFx0XHR2YWx1ZTogNCxcblx0XHRcdFx0bGFiZWw6IHRoaXMubGFiZWxzLm1vbnRoQXByLFxuXHRcdFx0fSwge1xuXHRcdFx0XHR2YWx1ZTogNSxcblx0XHRcdFx0bGFiZWw6IHRoaXMubGFiZWxzLm1vbnRoTWF5LFxuXHRcdFx0fSwge1xuXHRcdFx0XHR2YWx1ZTogNixcblx0XHRcdFx0bGFiZWw6IHRoaXMubGFiZWxzLm1vbnRoSnVuLFxuXHRcdFx0fSwge1xuXHRcdFx0XHR2YWx1ZTogNyxcblx0XHRcdFx0bGFiZWw6IHRoaXMubGFiZWxzLm1vbnRoSnVsLFxuXHRcdFx0fSwge1xuXHRcdFx0XHR2YWx1ZTogOCxcblx0XHRcdFx0bGFiZWw6IHRoaXMubGFiZWxzLm1vbnRoQXVnLFxuXHRcdFx0fSwge1xuXHRcdFx0XHR2YWx1ZTogOSxcblx0XHRcdFx0bGFiZWw6IHRoaXMubGFiZWxzLm1vbnRoU2VwLFxuXHRcdFx0fSwge1xuXHRcdFx0XHR2YWx1ZTogMTAsXG5cdFx0XHRcdGxhYmVsOiB0aGlzLmxhYmVscy5tb250aE9jdCxcblx0XHRcdH0sIHtcblx0XHRcdFx0dmFsdWU6IDExLFxuXHRcdFx0XHRsYWJlbDogdGhpcy5sYWJlbHMubW9udGhOb3YsXG5cdFx0XHR9LCB7XG5cdFx0XHRcdHZhbHVlOiAxMixcblx0XHRcdFx0bGFiZWw6IHRoaXMubGFiZWxzLm1vbnRoRGVjLFxuXHRcdFx0fSBdLFxuXHRcdFx0cmVjdXJyaW5nczogW1xuXHRcdFx0XHR7XG5cdFx0XHRcdFx0dmFsdWU6ICdkYWlseScsXG5cdFx0XHRcdFx0bGFiZWw6IHRoaXMubGFiZWxzLnJlY3VycmluZ0xhYmVsRGFpbHksXG5cdFx0XHRcdFx0c3VibGFiZWw6IHRoaXMubGFiZWxzLnJlY3VycmluZ1N1YkxhYmVsRGFpbHlcblx0XHRcdFx0fSxcblx0XHRcdFx0e1xuXHRcdFx0XHRcdHZhbHVlOiAnd2Vla2x5Jyxcblx0XHRcdFx0XHRsYWJlbDogdGhpcy5sYWJlbHMucmVjdXJyaW5nTGFiZWxXZWVrbHksXG5cdFx0XHRcdFx0c3VibGFiZWw6IHRoaXMubGFiZWxzLnJlY3VycmluZ1N1YkxhYmVsV2Vla2x5XG5cdFx0XHRcdH0sXG5cdFx0XHRcdHtcblx0XHRcdFx0XHR2YWx1ZTogJ21vbnRobHknLFxuXHRcdFx0XHRcdGxhYmVsOiB0aGlzLmxhYmVscy5yZWN1cnJpbmdMYWJlbE1vbnRobHksXG5cdFx0XHRcdFx0c3VibGFiZWw6IHRoaXMubGFiZWxzLnJlY3VycmluZ1N1YkxhYmVsTW9udGhseVxuXHRcdFx0XHR9LFxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0dmFsdWU6ICd5ZWFybHknLFxuXHRcdFx0XHRcdGxhYmVsOiB0aGlzLmxhYmVscy5yZWN1cnJpbmdMYWJlbFllYXJseSxcblx0XHRcdFx0XHRzdWJsYWJlbDogdGhpcy5sYWJlbHMucmVjdXJyaW5nU3ViTGFiZWxZZWFybHlcblx0XHRcdFx0fVxuXHRcdFx0XSxcblx0XHRcdHJlcXVpcmVkOiB0aGlzLnJlcXVpcmVkLFxuXHRcdFx0ZmllbGROYW1lOiB0aGlzLmZpZWxkTmFtZSxcblx0XHRcdGRhdGU6IHRoaXMuZGF0ZSxcblx0XHRcdHRpbWU6IHRoaXMudGltZSxcblx0XHRcdGlzRW5kRGF0ZTogdGhpcy5pc0VuZERhdGUsXG5cdFx0XHRlbmREYXRlOiB0aGlzLmVuZERhdGUsXG5cdFx0XHRlbmRUaW1lOiB0aGlzLmVuZFRpbWUsXG5cdFx0XHRpc1JlY3VycmluZzogdGhpcy5pc1JlY3VycmluZyxcblx0XHRcdHJlY3VycmluZzogdGhpcy5yZWN1cnJpbmcsXG5cdFx0XHRyZWN1cnJpbmdQZXJpb2Q6IHRoaXMucmVjdXJyaW5nUGVyaW9kLFxuXHRcdFx0d2Vla0RheXM6IHRoaXMud2Vla0RheXMsXG5cdFx0XHRtb250aGx5VHlwZTogdGhpcy5tb250aGx5VHlwZSxcblx0XHRcdG1vbnRoRGF5OiB0aGlzLm1vbnRoRGF5LFxuXHRcdFx0bW9udGhEYXlUeXBlOiB0aGlzLm1vbnRoRGF5VHlwZSxcblx0XHRcdG1vbnRoRGF5VHlwZVZhbHVlOiB0aGlzLm1vbnRoRGF5VHlwZVZhbHVlLFxuXHRcdFx0bW9udGg6IHRoaXMubW9udGgsXG5cdFx0XHRlbmQ6IHRoaXMuZW5kLFxuXHRcdFx0ZW5kQWZ0ZXI6IHRoaXMuZW5kQWZ0ZXIsXG5cdFx0XHRlbmRBZnRlckRhdGU6IHRoaXMuZW5kQWZ0ZXJEYXRlLFxuXHRcdFx0YWxsb3dUaW1lOiB0aGlzLmFsbG93VGltZSxcblx0XHR9O1xuXG5cdFx0dGhpcy4kY29udGFpbmVyLmh0bWwoIGZpZWxkVGVtcGxhdGUoIHRlbXBsYXRlRGF0YSApICk7XG5cblx0XHR0aGlzLmluaXRDYWxsYmFja3MuZm9yRWFjaCggKCBjYWxsYmFjayApID0+IHtcblx0XHRcdGNhbGxiYWNrKCB7XG5cdFx0XHRcdGluc3RhbmNlOiB0aGlzLFxuXHRcdFx0fSApO1xuXHRcdH0gKTtcblx0fVxuXG5cdHNlbGVjdG9ycyggd2hpY2ggKSB7XG5cblx0XHRjb25zdCBzZWxlY3RvcnMgPSB7XG5cdFx0XHRkYXRlOiAnaW5wdXRbbmFtZT1cIicgKyB0aGlzLmZpZWxkTmFtZSArICdbZGF0ZV1cIl0nLFxuXHRcdFx0dGltZTogJ2lucHV0W25hbWU9XCInICsgdGhpcy5maWVsZE5hbWUgKyAnW3RpbWVdXCJdJyxcblx0XHRcdGlzRW5kRGF0ZTogJ2lucHV0W25hbWU9XCInICsgdGhpcy5maWVsZE5hbWUgKyAnW2lzX2VuZF9kYXRlXVwiXScsXG5cdFx0XHRlbmREYXRlOiAnaW5wdXRbbmFtZT1cIicgKyB0aGlzLmZpZWxkTmFtZSArICdbZW5kX2RhdGVdXCJdJyxcblx0XHRcdGVuZFRpbWU6ICdzZWxlY3RbbmFtZT1cIicgKyB0aGlzLmZpZWxkTmFtZSArICdbZW5kX3RpbWVdXCJdJyxcblx0XHRcdGlzUmVjdXJyaW5nOiAnaW5wdXRbbmFtZT1cIicgKyB0aGlzLmZpZWxkTmFtZSArICdbaXNfcmVjdXJyaW5nXVwiXScsXG5cdFx0XHRyZWN1cnJpbmc6ICdzZWxlY3RbbmFtZT1cIicgKyB0aGlzLmZpZWxkTmFtZSArICdbcmVjdXJyaW5nXVwiXScsXG5cdFx0XHRyZWN1cnJpbmdQZXJpb2Q6ICdpbnB1dFtuYW1lPVwiJyArIHRoaXMuZmllbGROYW1lICsgJ1tyZWN1cnJpbmdfcGVyaW9kXVwiXScsXG5cdFx0XHR3ZWVrRGF5czogJ2lucHV0W25hbWU9XCInICsgdGhpcy5maWVsZE5hbWUgKyAnW3dlZWtfZGF5c11bXVwiXScsXG5cdFx0XHRtb250aGx5VHlwZTogJ2lucHV0W25hbWU9XCInICsgdGhpcy5maWVsZE5hbWUgKyAnW21vbnRobHlfdHlwZV1cIl0nLFxuXHRcdFx0bW9udGhEYXk6ICdzZWxlY3RbbmFtZT1cIicgKyB0aGlzLmZpZWxkTmFtZSArICdbbW9udGhfZGF5XVwiXScsXG5cdFx0XHRtb250aERheVR5cGU6ICdzZWxlY3RbbmFtZT1cIicgKyB0aGlzLmZpZWxkTmFtZSArICdbbW9udGhfZGF5X3R5cGVdXCJdJyxcblx0XHRcdG1vbnRoRGF5VHlwZVZhbHVlOiAnc2VsZWN0W25hbWU9XCInICsgdGhpcy5maWVsZE5hbWUgKyAnW21vbnRoX2RheV90eXBlX3ZhbHVlXVwiXScsXG5cdFx0XHRtb250aDogJ3NlbGVjdFtuYW1lPVwiJyArIHRoaXMuZmllbGROYW1lICsgJ1ttb250aF1cIl0nLFxuXHRcdFx0ZW5kOiAnc2VsZWN0W25hbWU9XCInICsgdGhpcy5maWVsZE5hbWUgKyAnW2VuZF1cIl0nLFxuXHRcdFx0ZW5kQWZ0ZXI6ICdpbnB1dFtuYW1lPVwiJyArIHRoaXMuZmllbGROYW1lICsgJ1tlbmRfYWZ0ZXJdXCJdJyxcblx0XHRcdGVuZEFmdGVyRGF0ZTogJ2lucHV0W25hbWU9XCInICsgdGhpcy5maWVsZE5hbWUgKyAnW2VuZF9hZnRlcl9kYXRlXVwiXScsXG5cdFx0fVxuXG5cdFx0aWYgKCB3aGljaCApIHtcblx0XHRcdHJldHVybiBzZWxlY3RvcnNbIHdoaWNoIF07XG5cdFx0fSBlbHNlIHtcblx0XHRcdHJldHVybiBzZWxlY3RvcnM7XG5cdFx0fVxuXG5cdH1cblxuXHR1cGRhdGUoIGRhdGEsIHNpbGVudCApIHtcblxuXHRcdHNpbGVudCA9IHNpbGVudCB8fCBmYWxzZTtcblxuXHRcdGxldCB1cGRhdGVkID0gZmFsc2U7XG5cblx0XHRmb3IgKCBjb25zdCBrZXkgaW4gZGF0YSApIHtcblx0XHRcdGlmICggdGhpc1sga2V5IF0gIT09IGRhdGFbIGtleSBdICkge1xuXHRcdFx0XHR0aGlzWyBrZXkgXSA9IGRhdGFbIGtleSBdO1xuXHRcdFx0XHR1cGRhdGVkID0gdHJ1ZTtcblx0XHRcdH1cblx0XHR9XG5cblx0XHR0aGlzLiRpbnB1dC5hdHRyKCAndmFsdWUnLCBKU09OLnN0cmluZ2lmeSggdGhpcy5nZXRQcm9wcygpICkgKTtcblxuXHRcdGlmICggISBzaWxlbnQgJiYgdXBkYXRlZCApIHtcblx0XHRcdHRoaXMucmVuZGVyKCk7XG5cdFx0fVxuXG5cdH1cblxuXHRldmVudHMoKSB7XG5cdFx0aWYgKCAncnJ1bGUnID09PSB0aGlzLmRhdGFGb3JtYXQgKSB7XG5cdFx0XHR0aGlzLmV2ZW50c1JSdWxlKCk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdHRoaXMuZXZlbnRzQ3VzdG9tKCk7XG5cdFx0fVxuXHR9XG5cblx0ZXZlbnRzQ3VzdG9tKCkge1xuXG5cdFx0dGhpcy4kY29udGFpbmVyLm9uKCAnY2hhbmdlJywgJy5qZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGQtLXN3aXRjaGVyLCAuZmllbGQtdHlwZS1zd2l0Y2hlciBpbnB1dCcsICggZXZlbnQgKSA9PiB7XG5cblx0XHRcdGNvbnN0ICRyb3cgID0galF1ZXJ5KCBldmVudC50YXJnZXQgKS5jbG9zZXN0KCAnLmpldC1lbmdpbmUtYWR2YW5jZWQtZGF0ZS1maWVsZF9fcmVwZWF0ZXItcm93JyApO1xuXHRcdFx0Y29uc3QgaW5kZXggPSAkcm93LmRhdGEoICdpbmRleCcgKTtcblxuXHRcdFx0aWYgKCAnaXNFbmREYXRlJyA9PT0gZXZlbnQudGFyZ2V0LmRhdGFzZXQua2V5ICkge1xuXHRcdFx0XHRmb3IgKCB2YXIgaSA9IDA7IGkgPCB0aGlzLmRhdGVzLmxlbmd0aDsgaSsrKSB7XG5cdFx0XHRcdFx0aWYgKCB0aGlzLmRhdGVzWyBpIF0udWlkID09IGluZGV4ICkge1xuXHRcdFx0XHRcdFx0aWYgKCB1bmRlZmluZWQgPT09IHRoaXMuZGF0ZXNbIGkgXS5pc0VuZERhdGUgKSB7XG5cdFx0XHRcdFx0XHRcdHRoaXMuZGF0ZXNbIGkgXS5pc0VuZERhdGUgPSBldmVudC50YXJnZXQuY2hlY2tlZDtcblx0XHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHRcdHRoaXMuZGF0ZXNbIGkgXS5pc0VuZERhdGUgPSAhIHRoaXMuZGF0ZXNbIGkgXS5pc0VuZERhdGU7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHQkcm93LnJlcGxhY2VXaXRoKCB0aGlzLmdldE5ld1JlcGVhdGVyRWwoIHRoaXMuZGF0ZXNbIGkgXSApICk7XG5cdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblx0XHRcdH1cblxuXHRcdFx0dGhpcy4kaW5wdXQuYXR0ciggJ3ZhbHVlJywgSlNPTi5zdHJpbmdpZnkoIHRoaXMuZ2V0UHJvcHMoKSApICk7XG5cblx0XHR9ICk7XG5cblx0XHRqUXVlcnkoIHdpbmRvdyApLm9uKCAnY3gtY29udHJvbC1jaGFuZ2UgY2hhbmdlJywgKCBldmVudCApID0+IHtcblxuXHRcdFx0Y29uc3QgdGFyZ2V0ID0gZXZlbnQuaW5wdXQgfHwgalF1ZXJ5KCBldmVudC50YXJnZXQgKTtcblxuXHRcdFx0aWYgKCB0YXJnZXQgJiYgdGFyZ2V0Lmhhc0NsYXNzKCAnamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkLS1jb250cm9sJyApICkge1xuXG5cdFx0XHRcdGNvbnN0ICRyb3cgID0gdGFyZ2V0LmNsb3Nlc3QoICcuamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19yZXBlYXRlci1yb3cnICk7XG5cdFx0XHRcdGNvbnN0IGluZGV4ID0gJHJvdy5kYXRhKCAnaW5kZXgnICk7XG5cblx0XHRcdFx0Zm9yICggdmFyIGkgPSAwOyBpIDwgdGhpcy5kYXRlcy5sZW5ndGg7IGkrKykge1xuXHRcdFx0XHRcdGlmICggdGhpcy5kYXRlc1sgaSBdLnVpZCA9PSBpbmRleCApIHtcblx0XHRcdFx0XHRcdGxldCB2YWx1ZSA9IGV2ZW50LmNvbnRyb2xTdGF0dXMgfHwgZXZlbnQudGFyZ2V0LnZhbHVlO1xuXHRcdFx0XHRcdFx0dGhpcy5kYXRlc1sgaSBdWyB0YXJnZXQuZGF0YSggJ2tleScgKSBdID0gdmFsdWU7XG5cdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblxuXHRcdFx0XHR0aGlzLiRpbnB1dC5hdHRyKCAndmFsdWUnLCBKU09OLnN0cmluZ2lmeSggdGhpcy5nZXRQcm9wcygpICkgKTtcblx0XHRcdH1cblx0XHR9ICk7XG5cblx0fVxuXG5cdGV2ZW50c1JSdWxlKCkge1xuXG5cdFx0Y29uc3Qgc3dpdGNoZXJzID0gW1xuXHRcdFx0J2lzRW5kRGF0ZScsXG5cdFx0XHQnaXNSZWN1cnJpbmcnXG5cdFx0XTtcblxuXHRcdGNvbnN0IHJlZ3VsYXJFbnZlbnRzID0gW1xuXHRcdFx0J2VuZCcsXG5cdFx0XHQncmVjdXJyaW5nJyxcblx0XHRcdCdtb250aCdcblx0XHRdO1xuXG5cdFx0Y29uc3Qgc2lsZW50RXZlbnRzID0gW1xuXHRcdFx0J3JlY3VycmluZ1BlcmlvZCcsXG5cdFx0XHQnbW9udGhseVR5cGUnLFxuXHRcdFx0J21vbnRoRGF5Jyxcblx0XHRcdCdtb250aERheVR5cGUnLFxuXHRcdFx0J21vbnRoRGF5VHlwZVZhbHVlJyxcblx0XHRcdCdlbmRBZnRlcicsXG5cdFx0XTtcblxuXHRcdGNvbnN0IGRhdGVzID0ge1xuXHRcdFx0ZGF0ZTogdGhpcy5maWVsZE5hbWUgKyAnW2RhdGVdJyxcblx0XHRcdHRpbWU6IHRoaXMuZmllbGROYW1lICsgJ1t0aW1lXScsXG5cdFx0XHRlbmREYXRlOiB0aGlzLmZpZWxkTmFtZSArICdbZW5kX2RhdGVdJyxcblx0XHRcdGVuZFRpbWU6IHRoaXMuZmllbGROYW1lICsgJ1tlbmRfdGltZV0nLFxuXHRcdFx0ZW5kQWZ0ZXJEYXRlOiB0aGlzLmZpZWxkTmFtZSArICdbZW5kX2FmdGVyX2RhdGVdJ1xuXHRcdH07XG5cblx0XHRmb3IgKCB2YXIgaSA9IDA7IGkgPCBzd2l0Y2hlcnMubGVuZ3RoOyBpKysgKSB7XG5cdFx0XHR0aGlzLiRjb250YWluZXIub24oICdjaGFuZ2UnLCB0aGlzLnNlbGVjdG9ycyggc3dpdGNoZXJzWyBpIF0gKSwgKCAoIGtleSwgZXZlbnQgKSA9PiB7XG5cdFx0XHRcdHRoaXMudXBkYXRlKCB7IFsga2V5IF06IGV2ZW50LnRhcmdldC5jaGVja2VkIH0gKTtcblx0XHRcdH0gKS5iaW5kKCB1bmRlZmluZWQsIHN3aXRjaGVyc1sgaSBdICkgKTtcblx0XHR9XG5cblx0XHRmb3IgKCB2YXIgaSA9IDA7IGkgPCByZWd1bGFyRW52ZW50cy5sZW5ndGg7IGkrKyApIHtcblx0XHRcdHRoaXMuJGNvbnRhaW5lci5vbiggJ2NoYW5nZScsIHRoaXMuc2VsZWN0b3JzKCByZWd1bGFyRW52ZW50c1sgaSBdICksICggKCBrZXksIGV2ZW50ICkgPT4ge1xuXHRcdFx0XHR0aGlzLnVwZGF0ZSggeyBbIGtleSBdOiBldmVudC50YXJnZXQudmFsdWUgfSApO1xuXHRcdFx0fSApLmJpbmQoIHVuZGVmaW5lZCwgcmVndWxhckVudmVudHNbIGkgXSApICk7XG5cdFx0fVxuXG5cdFx0Zm9yICggdmFyIGkgPSAwOyBpIDwgc2lsZW50RXZlbnRzLmxlbmd0aDsgaSsrICkge1xuXHRcdFx0dGhpcy4kY29udGFpbmVyLm9uKCAnY2hhbmdlJywgdGhpcy5zZWxlY3RvcnMoIHNpbGVudEV2ZW50c1sgaSBdICksICggKCBrZXksIGV2ZW50ICkgPT4ge1xuXHRcdFx0XHR0aGlzLnVwZGF0ZSggeyBbIGtleSBdOiBldmVudC50YXJnZXQudmFsdWUgfSwgdHJ1ZSApO1xuXHRcdFx0fSApLmJpbmQoIHVuZGVmaW5lZCwgc2lsZW50RXZlbnRzWyBpIF0gKSApO1xuXHRcdH1cblxuXHRcdGZvciAoIGNvbnN0IHByb3AgaW4gZGF0ZXMgKSB7XG5cblx0XHRcdC8vIEZhbGxiYWNrIGZvciBBZG1pbiBVSVxuXHRcdFx0alF1ZXJ5KCB3aW5kb3cgKS5vbiggJ2N4LWNvbnRyb2wtY2hhbmdlJywgKCAoIGtleSwgbmFtZSwgZXZlbnQgKSA9PiB7XG5cdFx0XHRcdGlmICggZXZlbnQuY29udHJvbE5hbWUgPT0gbmFtZSApIHtcblx0XHRcdFx0XHR0aGlzLnVwZGF0ZSggeyBbIGtleSBdOiBldmVudC5jb250cm9sU3RhdHVzIH0sIHRydWUgKTtcblx0XHRcdFx0fVxuXHRcdFx0fSApLmJpbmQoIHVuZGVmaW5lZCwgcHJvcCwgZGF0ZXNbIHByb3AgXSApICk7XG5cblx0XHRcdHRoaXMuJGNvbnRhaW5lci5vbiggJ2NoYW5nZScsIHRoaXMuc2VsZWN0b3JzKCBkYXRlc1sgcHJvcCBdICksICggKCBrZXksIG5hbWUsIGV2ZW50ICkgPT4ge1xuXHRcdFx0XHRpZiAoIGV2ZW50Py50YXJnZXQ/Lm5hbWUgPT0gbmFtZSApIHtcblx0XHRcdFx0XHR0aGlzLnVwZGF0ZSggeyBbIGtleSBdOiBldmVudC50YXJnZXQudmFsdWUgfSwgdHJ1ZSApO1xuXHRcdFx0XHR9XG5cdFx0XHR9ICkuYmluZCggdW5kZWZpbmVkLCBwcm9wLCBkYXRlc1sgcHJvcCBdICkgKTtcblx0XHR9XG5cblx0XHR0aGlzLiRjb250YWluZXIub24oICdjaGFuZ2UnLCB0aGlzLnNlbGVjdG9ycyggJ3dlZWtEYXlzJyApLCAoIGV2ZW50ICkgPT4ge1xuXG5cdFx0XHRjb25zdCBuZXdEYXlzID0gW107XG5cdFx0XHRjb25zdCBjaGVja2VkID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCggdGhpcy5zZWxlY3RvcnMoICd3ZWVrRGF5cycgKSArICc6Y2hlY2tlZCcgKTtcblxuXHRcdFx0aWYgKCBjaGVja2VkICYmIGNoZWNrZWQubGVuZ3RoICkge1xuXHRcdFx0XHRmb3IgKCB2YXIgaSA9IDA7IGkgPCBjaGVja2VkLmxlbmd0aDsgaSsrICkge1xuXHRcdFx0XHRcdG5ld0RheXMucHVzaCggY2hlY2tlZFsgaSBdLnZhbHVlICk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblxuXHRcdFx0dGhpcy51cGRhdGUoIHsgd2Vla0RheXM6IG5ld0RheXMgfSApO1xuXG5cdFx0fSApO1xuXG5cdH1cblxufVxuXG5leHBvcnQgZGVmYXVsdCBKZXRFbmdpbmVSZW5kZXJBZHZhbmNlZERhdGVGaWVsZDsiLCJpbXBvcnQgSmV0RW5naW5lQWR2YW5jZWREYXRlRmllbGRUZW1wbGF0ZSBmcm9tICcuL3RlbXBsYXRlJztcblxuY29uc3QgY3JlYXRlVGVtcGxhdGVFbmdpbmUgPSBmdW5jdGlvbigpIHtcblxuXHRjb25zdCB0ZW1wbGF0ZUVuZ2luZSA9IG5ldyBKZXRFbmdpbmVBZHZhbmNlZERhdGVGaWVsZFRlbXBsYXRlKCk7XG5cdGNvbnN0IHRlbXBsYXRlRW5naW5lRnVuYyA9IGZ1bmN0aW9uKCkge31cblxuXHRPYmplY3Quc2V0UHJvdG90eXBlT2YoIHRlbXBsYXRlRW5naW5lRnVuYywgdGVtcGxhdGVFbmdpbmUgKTtcblxuXHRyZXR1cm4gbmV3IFByb3h5KCB0ZW1wbGF0ZUVuZ2luZUZ1bmMsIHtcblx0XHRhcHBseTogZnVuY3Rpb24oIHRhcmdldCwgdGhpc0FyZywgYXJncyApIHtcblx0XHRcdGNvbnN0IFtjb250ZXh0XSA9IGFyZ3M7XG5cblx0XHRcdHN3aXRjaCAoIGNvbnRleHQgKSB7XG5cdFx0XHRcdGNhc2UgJ3JydWxlJzpcblx0XHRcdFx0XHRyZXR1cm4gdGVtcGxhdGVFbmdpbmUuZ2V0UmVjdXJyaW5nRmllbGRUZW1wbGF0ZS5iaW5kKCB0ZW1wbGF0ZUVuZ2luZSApO1xuXHRcdFx0XHRjYXNlICdiYXNlLWRhdGUnOlxuXHRcdFx0XHRcdHJldHVybiB0ZW1wbGF0ZUVuZ2luZS5nZXRCYXNlRGF0ZUZpZWxkVGVtcGxhdGUuYmluZCggdGVtcGxhdGVFbmdpbmUgKTtcblx0XHRcdFx0ZGVmYXVsdDpcblx0XHRcdFx0XHRyZXR1cm4gdGVtcGxhdGVFbmdpbmUucmVuZGVyLmJpbmQoIHRlbXBsYXRlRW5naW5lICk7XG5cdFx0XHR9XG5cdFx0fVxuXHR9ICk7XG59O1xuXG5leHBvcnQgZGVmYXVsdCBjcmVhdGVUZW1wbGF0ZUVuZ2luZTsiLCIvKipcbiAqIEFkdmFuY2VkIERhdGUgZmllbGQgdGVtcGxhdGUgY2xhc3MuXG4gKiBXb3JrcyBpbiBzaW1pbGFyIHdheSBhcyBiYWNrIGJvbmUgdGVtcGxhdGUuXG4gKiBDbGFzcyBjb250YWlucyBIVE1MIG1hcmt1cCB3aXRoIHRlbXBsYXRlIGxpdGVyYWxzLlxuICogRnJvbSBjb25zdHJ1Y3RvciByZWNlaXZlcyBkeW5hbWljIGRhdGEgYW5kIHJlbmRlcnMgdGFtcGxhdGUgYWNjb2RpbmcgcmV0cmlldmVkIGRhdGEuXG4gKiBAY2xhc3NcbiAqL1xuXG5jbGFzcyBKZXRFbmdpbmVBZHZhbmNlZERhdGVGaWVsZFRlbXBsYXRlIHtcblxuXHRyZW5kZXIoIGRhdGEgKSB7XG5cdFx0cmV0dXJuIHRoaXMuZ2V0VGVtcGxhdGUoIGRhdGEgKTtcblx0fVxuXG5cdC8qKlxuXHQgKiBHZXQgdGVtcGxhdGVcblx0ICpcblx0ICogQHJldHVybiB7c3RyaW5nfVxuXHQgKi9cblx0Z2V0VGVtcGxhdGUoIGRhdGEgKSB7XG5cdFx0cmV0dXJuIGBcblx0XHRcdCR7IHRoaXMuZ2V0QmFzZURhdGVGaWVsZFRlbXBsYXRlKCBkYXRhICkgfVxuXHRcdFx0JHsgdGhpcy5nZXRSZWN1cnJpbmdGaWVsZFRlbXBsYXRlKCBkYXRhICkgfVxuXHRcdGA7XG5cdH1cblxuXHRnZXRCYXNlRGF0ZUZpZWxkVGVtcGxhdGUoIGRhdGEgKSB7XG5cdFx0cmV0dXJuIGBcblx0XHRcdCR7IHRoaXMuZ2V0U3RhcnREYXRlRmllbGRUZW1wbGF0ZSggZGF0YSApIH1cblx0XHRcdCR7IHRoaXMuZ2V0RW5kRGF0ZUZpZWxkVGVtcGxhdGUoIGRhdGEgKSB9XG5cdFx0YDtcblx0fVxuXG5cdGdldFN0YXJ0RGF0ZUZpZWxkVGVtcGxhdGUoIGRhdGEgKSB7XG5cdFx0cmV0dXJuIGBcblx0XHRcdDxkaXYgY2xhc3M9XCJqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX2RhdGVcIj5cblx0XHRcdFx0PHNwYW4gY2xhc3M9XCJqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX2xhYmVsXCI+JHsgZGF0YS5sYWJlbHMuc3RhcnREYXRlIH08L3NwYW4+XG5cdFx0XHRcdDxkaXYgY2xhc3M9XCJqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX2RhdGUtd2FycCAkeyBkYXRhLnJlcXVpcmVkID8gJ2lzLXJlcXVpcmVkJyA6ICcnIH1cIiBkYXRhLWNvbnRyb2wtbmFtZT1cIiR7IGRhdGEuZmllbGROYW1lIH1bZGF0ZV1cIj5cblx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19kYXRlLWNvbnRyb2xzXCI+XG5cdFx0XHRcdFx0XHQ8aW5wdXRcblx0XHRcdFx0XHRcdFx0dHlwZT1cImRhdGVcIlxuXHRcdFx0XHRcdFx0XHRjbGFzcz1cImpldC1lbmdpbmUtYWR2YW5jZWQtZGF0ZS1maWVsZF9fZGF0ZS1pbnB1dCBqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGQtLWNvbnRyb2xcIlxuXHRcdFx0XHRcdFx0XHRuYW1lPVwiJHsgZGF0YS5maWVsZE5hbWUgfVtkYXRlXVwiXG5cdFx0XHRcdFx0XHRcdHBsYWNlaG9sZGVyPVwiU2VsZWN0IGRhdGUuLi5cIlxuXHRcdFx0XHRcdFx0XHR2YWx1ZT1cIiR7IGRhdGEuZGF0ZSB9XCJcblx0XHRcdFx0XHRcdFx0ZGF0YS1rZXk9XCJkYXRlXCJcblx0XHRcdFx0XHRcdFx0JHsgZGF0YS5yZXF1aXJlZCA/ICdyZXF1aXJlZCcgOiAnJyB9XG5cdFx0XHRcdFx0XHQ+XG5cdFx0XHRcdFx0XHQkeyBkYXRhLmFsbG93VGltZSA/IGBcblx0XHRcdFx0XHRcdFx0PGlucHV0XG5cdFx0XHRcdFx0XHRcdFx0dHlwZT1cInRpbWVcIlxuXHRcdFx0XHRcdFx0XHRcdGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX190aW1lLWlucHV0IGpldC1lbmdpbmUtYWR2YW5jZWQtZGF0ZS1maWVsZC0tY29udHJvbFwiXG5cdFx0XHRcdFx0XHRcdFx0bmFtZT1cIiR7IGRhdGEuZmllbGROYW1lIH1bdGltZV1cIlxuXHRcdFx0XHRcdFx0XHRcdHBsYWNlaG9sZGVyPVwiU2V0IHRpbWUuLi5cIlxuXHRcdFx0XHRcdFx0XHRcdHZhbHVlPVwiJHsgZGF0YS50aW1lIH1cIlxuXHRcdFx0XHRcdFx0XHRcdGRhdGEta2V5PVwidGltZVwiXG5cdFx0XHRcdFx0XHRcdFx0JHsgZGF0YS5yZXF1aXJlZCA/ICdyZXF1aXJlZCcgOiAnJyB9XG5cdFx0XHRcdFx0XHRcdD5cblx0XHRcdFx0XHRcdGAgOiAnJyB9XG5cdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0PGRpdiBjbGFzcz1cImN4LWNvbnRyb2xfX2Vycm9yXCI+PC9kaXY+XG5cdFx0XHRcdDwvZGl2PlxuXHRcdFx0PC9kaXY+XG5cdFx0YDtcblx0fVxuXG5cdGdldEVuZERhdGVGaWVsZFRlbXBsYXRlKCBkYXRhICkge1xuXHRcdHJldHVybiBgXG5cdFx0XHQ8ZGl2IGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19kYXRlXCI+XG5cdFx0XHRcdDxzcGFuIGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19sYWJlbFwiPiR7IGRhdGEubGFiZWxzLmhhc0VuZERhdGUgfTwvc3Bhbj5cblx0XHRcdFx0PGRpdiBjbGFzcz1cImpldC1lbmdpbmUtYWR2YW5jZWQtZGF0ZS1maWVsZF9fZW5kLWRhdGUtY29udHJvbHNcIj5cblx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwiZmllbGQtdHlwZS1zd2l0Y2hlclwiPlxuXHRcdFx0XHRcdFx0PGlucHV0IGlkPVwiJHsgZGF0YS5maWVsZE5hbWUgfV9faXNfZW5kX2RhdGVcIiBuYW1lPVwiJHsgZGF0YS5maWVsZE5hbWUgfVtpc19lbmRfZGF0ZV1cIiB0eXBlPVwiY2hlY2tib3hcIiByb2xlPVwic3dpdGNoXCIgY2xhc3M9XCJqZXQtZm9ybS1idWlsZGVyX19maWVsZFwiIHZhbHVlPVwiMVwiICR7IGRhdGEuaXNFbmREYXRlID8gJ2NoZWNrZWQnIDogJycgfSBkYXRhLWNhbGN1bGF0ZT1cIjFcIiBkYXRhLWpmYi1zeW5jPVwibnVsbFwiIGRhdGEta2V5PVwiaXNFbmREYXRlXCI+XG5cdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0JHsgZGF0YS5pc0VuZERhdGUgPyBgXG5cdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19kYXRlLXdhcnAgJHsgZGF0YS5yZXF1aXJlZCA/ICdjeC1jb250cm9sLXJlcXVpcmVkJyA6ICcnIH1cIiBkYXRhLWNvbnRyb2wtbmFtZT1cIiR7IGRhdGEuZmllbGROYW1lIH1bZW5kX2RhdGVdXCI+XG5cdFx0XHRcdFx0XHRcdDxkaXYgY2xhc3M9XCJqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX2RhdGUtY29udHJvbHNcIj5cblx0XHRcdFx0XHRcdFx0XHQ8aW5wdXRcblx0XHRcdFx0XHRcdFx0XHRcdHR5cGU9XCJkYXRlXCJcblx0XHRcdFx0XHRcdFx0XHRcdGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19lbmQtZGF0ZS1pbnB1dCBqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGQtLWNvbnRyb2xcIlxuXHRcdFx0XHRcdFx0XHRcdFx0bmFtZT1cIiR7IGRhdGEuZmllbGROYW1lIH1bZW5kX2RhdGVdXCJcblx0XHRcdFx0XHRcdFx0XHRcdHBsYWNlaG9sZGVyPVwiU2VsZWN0IGRhdGUuLi5cIlxuXHRcdFx0XHRcdFx0XHRcdFx0dmFsdWU9XCIkeyBkYXRhLmVuZERhdGUgfVwiXG5cdFx0XHRcdFx0XHRcdFx0XHRkYXRhLWtleT1cImVuZERhdGVcIlxuXHRcdFx0XHRcdFx0XHRcdFx0JHsgZGF0YS5yZXF1aXJlZCA/ICdyZXF1aXJlZCcgOiAnJyB9XG5cdFx0XHRcdFx0XHRcdFx0PlxuXHRcdFx0XHRcdFx0XHRcdCR7IGRhdGEuYWxsb3dUaW1lID8gYFxuXHRcdFx0XHRcdFx0XHRcdFx0PGlucHV0XG5cdFx0XHRcdFx0XHRcdFx0XHRcdHR5cGU9XCJ0aW1lXCJcblx0XHRcdFx0XHRcdFx0XHRcdFx0Y2xhc3M9XCJqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX2VuZC10aW1lLWlucHV0IGpldC1lbmdpbmUtYWR2YW5jZWQtZGF0ZS1maWVsZC0tY29udHJvbFwiXG5cdFx0XHRcdFx0XHRcdFx0XHRcdG5hbWU9XCIkeyBkYXRhLmZpZWxkTmFtZSB9W2VuZF90aW1lXVwiXG5cdFx0XHRcdFx0XHRcdFx0XHRcdHBsYWNlaG9sZGVyPVwiU2V0IHRpbWUuLi5cIlxuXHRcdFx0XHRcdFx0XHRcdFx0XHR2YWx1ZT1cIiR7IGRhdGEuZW5kVGltZSB9XCJcblx0XHRcdFx0XHRcdFx0XHRcdFx0ZGF0YS1rZXk9XCJlbmRUaW1lXCJcblx0XHRcdFx0XHRcdFx0XHRcdFx0JHsgZGF0YS5yZXF1aXJlZCA/ICdyZXF1aXJlZCcgOiAnJyB9XG5cdFx0XHRcdFx0XHRcdFx0XHQ+XG5cdFx0XHRcdFx0XHRcdFx0YCA6ICcnIH1cblx0XHRcdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0XHRcdDxkaXYgY2xhc3M9XCJjeC1jb250cm9sX19lcnJvclwiPjwvZGl2PlxuXHRcdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0YCA6ICcnIH1cblx0XHRcdFx0PC9kaXY+XG5cdFx0XHQ8L2Rpdj5cblx0XHRgO1xuXHR9XG5cblx0Z2V0UmVjdXJyaW5nRmllbGRUZW1wbGF0ZSggZGF0YSApIHtcblx0XHRyZXR1cm4gYFxuXHRcdFx0PGRpdiBjbGFzcz1cImpldC1lbmdpbmUtYWR2YW5jZWQtZGF0ZS1maWVsZF9faXMtcmVjdXJyaW5nXCI+XG5cdFx0XHRcdDxsYWJlbCBjbGFzcz1cImpldC1lbmdpbmUtYWR2YW5jZWQtZGF0ZS1maWVsZF9fbGFiZWxcIiBmb3I9XCIkeyBkYXRhLmZpZWxkTmFtZSB9X19pc19yZWN1cnJpbmdcIj5cblx0XHRcdFx0XHQkeyBkYXRhLmxhYmVscy5pc1JlY3VycmluZyB9XG5cdFx0XHRcdDwvbGFiZWw+XG5cdFx0XHRcdDxkaXYgY2xhc3M9XCJmaWVsZC10eXBlLXN3aXRjaGVyXCI+XG5cdFx0XHRcdFx0PGlucHV0IGlkPVwiJHsgZGF0YS5maWVsZE5hbWUgfV9faXNfcmVjdXJyaW5nXCIgbmFtZT1cIiR7IGRhdGEuZmllbGROYW1lIH1baXNfcmVjdXJyaW5nXVwiIHR5cGU9XCJjaGVja2JveFwiIHJvbGU9XCJzd2l0Y2hcIiBjbGFzcz1cImpldC1mb3JtLWJ1aWxkZXJfX2ZpZWxkXCIgdmFsdWU9XCIxXCIgJHsgZGF0YS5pc1JlY3VycmluZyA/ICdjaGVja2VkJyA6ICcnIH0gZGF0YS1jYWxjdWxhdGU9XCIxXCIgZGF0YS1qZmItc3luYz1cIm51bGxcIiBkYXRhLWtleT1cImlzUmVjdXJyaW5nXCI+XG5cdFx0XHRcdDwvZGl2PlxuXHRcdFx0PC9kaXY+XG5cdFx0XHQkeyBkYXRhLmlzUmVjdXJyaW5nID8gYFxuXHRcdFx0XHQ8ZGl2IGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19yZWN1cnJpbmctd3JhcFwiPlxuXHRcdFx0XHRcdDxkaXYgY2xhc3M9XCJqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX3JlY3VycmluZy1yb3dcIj5cblx0XHRcdFx0XHRcdDxkaXYgY2xhc3M9XCJqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX3JlY3VycmluZy1sYWJlbCBqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX2xhYmVsXCI+XG5cdFx0XHRcdFx0XHRcdCR7IGRhdGEubGFiZWxzLnJlcGVhdCB9XG5cdFx0XHRcdFx0XHQ8L2Rpdj5cblx0XHRcdFx0XHRcdDxkaXYgY2xhc3M9XCJqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX3JlY3VycmluZy1jb250ZW50XCI+XG5cdFx0XHRcdFx0XHRcdDxzZWxlY3QgbmFtZT1cIiR7IGRhdGEuZmllbGROYW1lIH1bcmVjdXJyaW5nXVwiIGNsYXNzPVwiY3gtdWktc2VsZWN0XCI+XG5cdFx0XHRcdFx0XHRcdFx0JHsgZGF0YS5yZWN1cnJpbmdzLm1hcChyZWN1cnJpbmcgPT4gYFxuXHRcdFx0XHRcdFx0XHRcdFx0PG9wdGlvblxuXHRcdFx0XHRcdFx0XHRcdFx0XHR2YWx1ZT1cIiR7IHJlY3VycmluZy52YWx1ZSB9XCJcblx0XHRcdFx0XHRcdFx0XHRcdFx0JHsgZGF0YS5yZWN1cnJpbmcgPT09IHJlY3VycmluZy52YWx1ZSA/ICdzZWxlY3RlZCcgOiAnJyB9XG5cdFx0XHRcdFx0XHRcdFx0XHQ+XG5cdFx0XHRcdFx0XHRcdFx0XHRcdCR7IHJlY3VycmluZy5sYWJlbCB9XG5cdFx0XHRcdFx0XHRcdFx0XHQ8L29wdGlvbj5cblx0XHRcdFx0XHRcdFx0XHRgKS5qb2luKCcnKSB9XG5cdFx0XHRcdFx0XHRcdDwvc2VsZWN0PlxuXHRcdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19yZWN1cnJpbmctbGFiZWxcIj5cblx0XHRcdFx0XHRcdFx0XHQkeyBkYXRhLmxhYmVscy5ldmVyeSB9XG5cdFx0XHRcdFx0XHRcdDwvZGl2PlxuXHRcdFx0XHRcdFx0XHQ8aW5wdXRcblx0XHRcdFx0XHRcdFx0XHR0eXBlPVwibnVtYmVyXCJcblx0XHRcdFx0XHRcdFx0XHRuYW1lPVwiJHsgZGF0YS5maWVsZE5hbWUgfVtyZWN1cnJpbmdfcGVyaW9kXVwiXG5cdFx0XHRcdFx0XHRcdFx0bWluPVwiMVwiXG5cdFx0XHRcdFx0XHRcdFx0dmFsdWU9XCIkeyBkYXRhLnJlY3VycmluZ1BlcmlvZCB9XCJcblx0XHRcdFx0XHRcdFx0XHRjbGFzcz1cImN4LXVpLXRleHRcIlxuXHRcdFx0XHRcdFx0XHQ+XG5cdFx0XHRcdFx0XHRcdCR7IGRhdGEucmVjdXJyaW5ncy5tYXAocmVjdXJyaW5nID0+XG5cdFx0XHRcdFx0XHRcdFx0ZGF0YS5yZWN1cnJpbmcgPT09IHJlY3VycmluZy52YWx1ZSA/IGBcblx0XHRcdFx0XHRcdFx0XHRcdDxkaXYgY2xhc3M9XCJqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX3JlY3VycmluZy1sYWJlbFwiPlxuXHRcdFx0XHRcdFx0XHRcdFx0XHQkeyByZWN1cnJpbmcuc3VibGFiZWwgfVxuXHRcdFx0XHRcdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0XHRcdFx0YCA6ICcnXG5cdFx0XHRcdFx0XHRcdCkuam9pbignJykgfVxuXHRcdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0JHsgZGF0YS5yZWN1cnJpbmcgIT09ICdkYWlseScgPyBgXG5cdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19yZWN1cnJpbmctcm93XCI+XG5cdFx0XHRcdFx0XHRcdDxkaXYgY2xhc3M9XCJqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX3JlY3VycmluZy1sYWJlbCBqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX2xhYmVsIGxhYmVsLXdlZWtkYXlzXCI+XG5cdFx0XHRcdFx0XHRcdFx0Jm5ic3A7XG5cdFx0XHRcdFx0XHRcdDwvZGl2PlxuXHRcdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19yZWN1cnJpbmctY29udGVudFwiPlxuXHRcdFx0XHRcdFx0XHRcdCR7IGRhdGEucmVjdXJyaW5nID09PSAnd2Vla2x5JyA/IGBcblx0XHRcdFx0XHRcdFx0XHRcdDxkaXYgY2xhc3M9XCJqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX3dlZWtkYXlzXCI+XG5cdFx0XHRcdFx0XHRcdFx0XHRcdCR7IGRhdGEud2Vla2RheXNDb25maWcubWFwKGRheSA9PiBgXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0PGxhYmVsIGFyaWEtbGFiZWw9XCIkeyBkYXkubGFiZWwgfVwiPlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0PGlucHV0XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdHR5cGU9XCJjaGVja2JveFwiXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdHZhbHVlPVwiJHsgZGF5LnZhbHVlIH1cIlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRuYW1lPVwiJHsgZGF0YS5maWVsZE5hbWUgfVt3ZWVrX2RheXNdW11cIlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQkeyBkYXRhLndlZWtEYXlzLmluY2x1ZGVzKCcnICsgZGF5LnZhbHVlKSA/ICdjaGVja2VkJyA6ICcnIH1cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdD5cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdDxzcGFuIGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX193ZWVrZGF5LWxhYmVsXCI+XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCR7IGRheS5sYWJlbCB9XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ8L3NwYW4+XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ8c3BhbiBjbGFzcz1cImpldC1lbmdpbmUtYWR2YW5jZWQtZGF0ZS1maWVsZF9fd2Vla2RheS1tYXJrZXJcIj48L3NwYW4+XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0PC9sYWJlbD5cblx0XHRcdFx0XHRcdFx0XHRcdFx0YCkuam9pbignJykgfVxuXHRcdFx0XHRcdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0XHRcdFx0YCA6ICcnIH1cblx0XHRcdFx0XHRcdFx0XHQkeyAoZGF0YS5yZWN1cnJpbmcgPT09ICdtb250aGx5JyB8fCBkYXRhLnJlY3VycmluZyA9PT0gJ3llYXJseScpID8gYFxuXHRcdFx0XHRcdFx0XHRcdFx0PGRpdiBjbGFzcz1cImpldC1lbmdpbmUtYWR2YW5jZWQtZGF0ZS1maWVsZF9fbW9udGhseVwiPlxuXHRcdFx0XHRcdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19tb250aGx5LXJvd1wiPlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdDxsYWJlbD5cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdDxpbnB1dFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR0eXBlPVwicmFkaW9cIlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR2YWx1ZT1cIm9uX2RheVwiXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdG5hbWU9XCIkeyBkYXRhLmZpZWxkTmFtZSB9W21vbnRobHlfdHlwZV1cIlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQkeyBkYXRhLm1vbnRobHlUeXBlID09PSAnb25fZGF5JyA/ICdjaGVja2VkJyA6ICcnIH1cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdD5cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCR7IGRhdGEubGFiZWxzLm9uRGF5IH1cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ8L2xhYmVsPlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCR7IGRhdGEucmVjdXJyaW5nID09PSAneWVhcmx5JyA/IGBcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdDxzZWxlY3QgbmFtZT1cIiR7IGRhdGEuZmllbGROYW1lIH1bbW9udGhdXCIgY2xhc3M9XCJjeC11aS1zZWxlY3RcIj5cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JHsgZGF0YS5tb250aHMubWFwKG1vbnRoID0+IGBcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ8b3B0aW9uXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR2YWx1ZT1cIiR7IG1vbnRoLnZhbHVlIH1cIlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JHsgbW9udGgudmFsdWUgPT0gZGF0YS5tb250aCA/ICdzZWxlY3RlZCcgOiAnJyB9XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0PlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JHsgbW9udGgubGFiZWwgfVxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdDwvb3B0aW9uPlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRgKS5qb2luKCcnKSB9XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ8L3NlbGVjdD5cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRgIDogJycgfVxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdDxzZWxlY3QgbmFtZT1cIiR7IGRhdGEuZmllbGROYW1lIH1bbW9udGhfZGF5XVwiIGNsYXNzPVwiY3gtdWktc2VsZWN0XCI+XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQkeyBBcnJheS5mcm9tKHsgbGVuZ3RoOiAzMSB9LCAoXywgaSkgPT4gaSArIDEpLm1hcChpID0+IGBcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0PG9wdGlvblxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdHZhbHVlPVwiJHsgaSB9XCJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQkeyBpID09IGRhdGEubW9udGhEYXkgPyAnc2VsZWN0ZWQnIDogJycgfVxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ+XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JHsgaSB9XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdDwvb3B0aW9uPlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0YCkuam9pbignJykgfVxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdDwvc2VsZWN0PlxuXHRcdFx0XHRcdFx0XHRcdFx0XHQ8L2Rpdj5cblx0XHRcdFx0XHRcdFx0XHRcdFx0PGRpdiBjbGFzcz1cImpldC1lbmdpbmUtYWR2YW5jZWQtZGF0ZS1maWVsZF9fbW9udGhseS1yb3dcIj5cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ8bGFiZWw+XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ8aW5wdXRcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0dHlwZT1cInJhZGlvXCJcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0dmFsdWU9XCJvbl9kYXlfdHlwZVwiXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdG5hbWU9XCIkeyBkYXRhLmZpZWxkTmFtZSB9W21vbnRobHlfdHlwZV1cIlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQkeyBkYXRhLm1vbnRobHlUeXBlID09PSAnb25fZGF5X3R5cGUnID8gJ2NoZWNrZWQnIDogJycgfVxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0PlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JHsgZGF0YS5sYWJlbHMub25UaGUgfVxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdDwvbGFiZWw+XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0PHNlbGVjdCBuYW1lPVwiJHsgZGF0YS5maWVsZE5hbWUgfVttb250aF9kYXlfdHlwZV1cIiBjbGFzcz1cImN4LXVpLXNlbGVjdFwiPlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0PG9wdGlvbiB2YWx1ZT1cImZpcnN0XCIgJHsgZGF0YS5tb250aERheVR5cGUgPT09ICdmaXJzdCcgPyAnc2VsZWN0ZWQnIDogJycgfT4keyBkYXRhLmxhYmVscy5maXJzdCB9PC9vcHRpb24+XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ8b3B0aW9uIHZhbHVlPVwic2Vjb25kXCIgJHsgZGF0YS5tb250aERheVR5cGUgPT09ICdzZWNvbmQnID8gJ3NlbGVjdGVkJyA6ICcnIH0+JHsgZGF0YS5sYWJlbHMuc2Vjb25kIH08L29wdGlvbj5cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdDxvcHRpb24gdmFsdWU9XCJ0aGlyZFwiICR7IGRhdGEubW9udGhEYXlUeXBlID09PSAndGhpcmQnID8gJ3NlbGVjdGVkJyA6ICcnIH0+JHsgZGF0YS5sYWJlbHMudGhpcmQgfTwvb3B0aW9uPlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0PG9wdGlvbiB2YWx1ZT1cImZvdXJ0aFwiICR7IGRhdGEubW9udGhEYXlUeXBlID09PSAnZm91cnRoJyA/ICdzZWxlY3RlZCcgOiAnJyB9PiR7IGRhdGEubGFiZWxzLmZvdXJ0aCB9PC9vcHRpb24+XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ8b3B0aW9uIHZhbHVlPVwibGFzdFwiICR7IGRhdGEubW9udGhEYXlUeXBlID09PSAnbGFzdCcgPyAnc2VsZWN0ZWQnIDogJycgfT4keyBkYXRhLmxhYmVscy5sYXN0IH08L29wdGlvbj5cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ8L3NlbGVjdD5cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ8c2VsZWN0IG5hbWU9XCIkeyBkYXRhLmZpZWxkTmFtZSB9W21vbnRoX2RheV90eXBlX3ZhbHVlXVwiIGNsYXNzPVwiY3gtdWktc2VsZWN0XCI+XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQkeyBkYXRhLndlZWtkYXlzQ29uZmlnLm1hcChkYXkgPT4gYFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ8b3B0aW9uXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0dmFsdWU9XCIkeyBkYXkudmFsdWUgfVwiXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JHsgZGF5LnZhbHVlID09IGRhdGEubW9udGhEYXlUeXBlVmFsdWUgPyAnc2VsZWN0ZWQnIDogJycgfVxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ+XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JHsgZGF5LmxhYmVsIH1cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0PC9vcHRpb24+XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRgKS5qb2luKCcnKSB9XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ8b3B0aW9uIHZhbHVlPVwiZGF5XCIgJHsgZGF0YS5tb250aERheVR5cGVWYWx1ZSA9PT0gJ2RheScgPyAnc2VsZWN0ZWQnIDogJycgfT4keyBkYXRhLmxhYmVscy5kYXkgfTwvb3B0aW9uPlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdDwvc2VsZWN0PlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCR7IGRhdGEucmVjdXJyaW5nID09PSAneWVhcmx5JyA/IGBcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdDxzZWxlY3QgbmFtZT1cIiR7IGRhdGEuZmllbGROYW1lIH1bbW9udGhdXCIgY2xhc3M9XCJjeC11aS1zZWxlY3RcIj5cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JHsgZGF0YS5tb250aHMubWFwKG1vbnRoID0+IGBcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ8b3B0aW9uXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHR2YWx1ZT1cIiR7IG1vbnRoLnZhbHVlIH1cIlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JHsgbW9udGgudmFsdWUgPT0gZGF0YS5tb250aCA/ICdzZWxlY3RlZCcgOiAnJyB9XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0PlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0JHsgbW9udGgubGFiZWwgfVxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdDwvb3B0aW9uPlxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHRgKS5qb2luKCcnKSB9XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0XHQ8L3NlbGVjdD5cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRgIDogJycgfVxuXHRcdFx0XHRcdFx0XHRcdFx0XHQ8L2Rpdj5cblx0XHRcdFx0XHRcdFx0XHRcdDwvZGl2PlxuXHRcdFx0XHRcdFx0XHRcdGAgOiAnJyB9XG5cdFx0XHRcdFx0XHRcdDwvZGl2PlxuXHRcdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0YCA6ICcnIH1cblx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19yZWN1cnJpbmctcm93XCI+XG5cdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19yZWN1cnJpbmctbGFiZWwgamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19sYWJlbFwiPlxuXHRcdFx0XHRcdFx0XHQkeyBkYXRhLmxhYmVscy5lbmQgfVxuXHRcdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwiamV0LWVuZ2luZS1hZHZhbmNlZC1kYXRlLWZpZWxkX19yZWN1cnJpbmctY29udGVudFwiPlxuXHRcdFx0XHRcdFx0XHQ8c2VsZWN0IG5hbWU9XCIkeyBkYXRhLmZpZWxkTmFtZSB9W2VuZF1cIiBjbGFzcz1cImN4LXVpLXNlbGVjdFwiPlxuXHRcdFx0XHRcdFx0XHRcdDxvcHRpb24gdmFsdWU9XCJhZnRlclwiICR7IGRhdGEuZW5kID09PSAnYWZ0ZXInID8gJ3NlbGVjdGVkJyA6ICcnIH0+JHsgZGF0YS5sYWJlbHMuYWZ0ZXIgfTwvb3B0aW9uPlxuXHRcdFx0XHRcdFx0XHRcdDxvcHRpb24gdmFsdWU9XCJvbl9kYXRlXCIgJHsgZGF0YS5lbmQgPT09ICdvbl9kYXRlJyA/ICdzZWxlY3RlZCcgOiAnJyB9PiR7IGRhdGEubGFiZWxzLm9uRGF0ZSB9PC9vcHRpb24+XG5cdFx0XHRcdFx0XHRcdDwvc2VsZWN0PlxuXHRcdFx0XHRcdFx0XHQkeyBkYXRhLmVuZCA9PT0gJ2FmdGVyJyA/IGBcblx0XHRcdFx0XHRcdFx0XHQ8aW5wdXRcblx0XHRcdFx0XHRcdFx0XHRcdHR5cGU9XCJudW1iZXJcIlxuXHRcdFx0XHRcdFx0XHRcdFx0bmFtZT1cIiR7IGRhdGEuZmllbGROYW1lIH1bZW5kX2FmdGVyXVwiXG5cdFx0XHRcdFx0XHRcdFx0XHRtaW49XCIyXCJcblx0XHRcdFx0XHRcdFx0XHRcdHZhbHVlPVwiJHsgZGF0YS5lbmRBZnRlciB9XCJcblx0XHRcdFx0XHRcdFx0XHRcdGNsYXNzPVwiY3gtdWktdGV4dFwiXG5cdFx0XHRcdFx0XHRcdFx0PlxuXHRcdFx0XHRcdFx0XHRcdDxkaXYgY2xhc3M9XCJqZXQtZW5naW5lLWFkdmFuY2VkLWRhdGUtZmllbGRfX3JlY3VycmluZy1sYWJlbFwiPlxuXHRcdFx0XHRcdFx0XHRcdFx0JHsgZGF0YS5sYWJlbHMuaXRlcmF0aW9ucyB9XG5cdFx0XHRcdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0XHRcdGAgOiAnJyB9XG5cdFx0XHRcdFx0XHRcdCR7IGRhdGEuZW5kID09PSAnb25fZGF0ZScgPyBgXG5cdFx0XHRcdFx0XHRcdFx0PGlucHV0XG5cdFx0XHRcdFx0XHRcdFx0XHR0eXBlPVwiZGF0ZVwiXG5cdFx0XHRcdFx0XHRcdFx0XHRjbGFzcz1cImpldC1lbmdpbmUtYWR2YW5jZWQtZGF0ZS1maWVsZF9fZGF0ZS1pbnB1dFwiXG5cdFx0XHRcdFx0XHRcdFx0XHRuYW1lPVwiJHsgZGF0YS5maWVsZE5hbWUgfVtlbmRfYWZ0ZXJfZGF0ZV1cIlxuXHRcdFx0XHRcdFx0XHRcdFx0cGxhY2Vob2xkZXI9XCJTZWxlY3QgZGF0ZS4uLlwiXG5cdFx0XHRcdFx0XHRcdFx0XHR2YWx1ZT1cIiR7IGRhdGEuZW5kQWZ0ZXJEYXRlIH1cIlxuXHRcdFx0XHRcdFx0XHRcdFx0cmVxdWlyZWRcblx0XHRcdFx0XHRcdFx0XHQ+XG5cdFx0XHRcdFx0XHRcdGAgOiAnJyB9XG5cdFx0XHRcdFx0XHQ8L2Rpdj5cblx0XHRcdFx0XHQ8L2Rpdj5cblx0XHRcdFx0PC9kaXY+XG5cdFx0XHRgIDogJycgfVxuXHRcdGA7XG5cdH1cblxufVxuXG5leHBvcnQgZGVmYXVsdCBKZXRFbmdpbmVBZHZhbmNlZERhdGVGaWVsZFRlbXBsYXRlOyIsIi8vIFRoZSBtb2R1bGUgY2FjaGVcbnZhciBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX18gPSB7fTtcblxuLy8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbmZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG5cdHZhciBjYWNoZWRNb2R1bGUgPSBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdO1xuXHRpZiAoY2FjaGVkTW9kdWxlICE9PSB1bmRlZmluZWQpIHtcblx0XHRyZXR1cm4gY2FjaGVkTW9kdWxlLmV4cG9ydHM7XG5cdH1cblx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcblx0dmFyIG1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF0gPSB7XG5cdFx0Ly8gbm8gbW9kdWxlLmlkIG5lZWRlZFxuXHRcdC8vIG5vIG1vZHVsZS5sb2FkZWQgbmVlZGVkXG5cdFx0ZXhwb3J0czoge31cblx0fTtcblxuXHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cblx0X193ZWJwYWNrX21vZHVsZXNfX1ttb2R1bGVJZF0obW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cblx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcblx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xufVxuXG4iLCIvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9ucyBmb3IgaGFybW9ueSBleHBvcnRzXG5fX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSAoZXhwb3J0cywgZGVmaW5pdGlvbikgPT4ge1xuXHRmb3IodmFyIGtleSBpbiBkZWZpbml0aW9uKSB7XG5cdFx0aWYoX193ZWJwYWNrX3JlcXVpcmVfXy5vKGRlZmluaXRpb24sIGtleSkgJiYgIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBrZXkpKSB7XG5cdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywga2V5LCB7IGVudW1lcmFibGU6IHRydWUsIGdldDogZGVmaW5pdGlvbltrZXldIH0pO1xuXHRcdH1cblx0fVxufTsiLCJfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSAob2JqLCBwcm9wKSA9PiAoT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iaiwgcHJvcCkpIiwiLy8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuX193ZWJwYWNrX3JlcXVpcmVfXy5yID0gKGV4cG9ydHMpID0+IHtcblx0aWYodHlwZW9mIFN5bWJvbCAhPT0gJ3VuZGVmaW5lZCcgJiYgU3ltYm9sLnRvU3RyaW5nVGFnKSB7XG5cdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFN5bWJvbC50b1N0cmluZ1RhZywgeyB2YWx1ZTogJ01vZHVsZScgfSk7XG5cdH1cblx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsICdfX2VzTW9kdWxlJywgeyB2YWx1ZTogdHJ1ZSB9KTtcbn07IiwiaW1wb3J0IEpldEVuZ2luZVJlbmRlckFkdmFuY2VkRGF0ZUZpZWxkIGZyb20gJ2ZpZWxkLXVpL2FkdmFuY2VkLWRhdGUtZmllbGQtcmVuZGVyJztcbmltcG9ydCBjcmVhdGVUZW1wbGF0ZUVuZ2luZSBmcm9tICdmaWVsZC11aS90ZW1wbGF0ZS1mYWN0b3J5JztcblxuY29uc3Qge1xuXHRhZGRGaWx0ZXJcbn0gPSB3aW5kb3cuSmV0UGx1Z2lucy5ob29rcztcblxuY29uc3Qge1xuXHRJbnB1dERhdGEsXG59ID0gSmV0Rm9ybUJ1aWxkZXJBYnN0cmFjdDtcblxuZnVuY3Rpb24gSmV0RW5naW5lQWR2YW5jZWREYXRlKCkge1xuXG5cdElucHV0RGF0YS5jYWxsKCB0aGlzICk7XG5cblx0dGhpcy5pc1N1cHBvcnRlZCA9IGZ1bmN0aW9uICggbm9kZSApIHtcblx0XHRyZXR1cm4gbm9kZS5jbGFzc0xpc3QuY29udGFpbnMoICdqZXQtZm9ybS1idWlsZGVyLWFkdmFuY2VkLWRhdGVfX2lucHV0JyApO1xuXHR9O1xuXG5cdHRoaXMuYWRkTGlzdGVuZXJzID0gZnVuY3Rpb24gKCkge1xuXG5cdFx0Y29uc3QgWyBub2RlIF0gPSB0aGlzLm5vZGVzO1xuXG5cdFx0bm9kZS5hZGRFdmVudExpc3RlbmVyKCAnYmx1cicsIGV2ZW50ID0+IHtcblx0XHRcdHRoaXMudmFsdWUuY3VycmVudCA9IGV2ZW50LnRhcmdldC52YWx1ZTtcblx0XHR9ICk7XG5cblx0XHRqUXVlcnkoIG5vZGUgKS5vbiggJ2NoYW5nZScsICggZSwgdGlueWNvbG9yICkgPT4ge1xuXHRcdFx0dGhpcy52YWx1ZS5jdXJyZW50ID0gdGlueWNvbG9yPy50b1N0cmluZygpID8/ICcnO1xuXHRcdH0gKTtcblx0fTtcblxuXHR0aGlzLnNldE5vZGUgPSBmdW5jdGlvbiAoIG5vZGUgKSB7XG5cblx0XHRJbnB1dERhdGEucHJvdG90eXBlLnNldE5vZGUuY2FsbCggdGhpcywgbm9kZSApO1xuXG5cdFx0dGhpcy4kaW5wdXQgPSBqUXVlcnkoIG5vZGUgKTtcblxuXHRcdHRoaXMubGlzdGVuRm9ybVBhZ2VDaGFuZ2UgPSBmYWxzZTtcblxuXHRcdGlmICggISB0aGlzLmxpc3RlbkZvcm1QYWdlQ2hhbmdlICkge1xuXHRcdFx0alF1ZXJ5KCB3aW5kb3cpLm9uKCAnamV0LWZvcm0tYnVpbGRlci9zd2l0Y2gtcGFnZScsICgpID0+IHtcblx0XHRcdFx0Ly8gdGhpcy5pbml0RGF0ZUZpZWxkKCk7XG5cdFx0XHR9ICk7XG5cdFx0XHR0aGlzLmxpc3RlbkZvcm1QYWdlQ2hhbmdlID0gdHJ1ZTtcblx0XHR9XG5cblx0XHR0aGlzLmluaXREYXRlRmllbGQoKTtcblx0fTtcblxuXHR0aGlzLmluaXREYXRlRmllbGQgPSBmdW5jdGlvbigpIHtcblxuXHRcdGxldCBsYWJlbHMgPSB7fTtcblxuXHRcdGlmICggdGhpcy4kaW5wdXQuZGF0YSggJ2xhYmVscycgKSApIHtcblx0XHRcdGxhYmVscyA9IHRoaXMuJGlucHV0LmRhdGEoICdsYWJlbHMnICk7XG5cdFx0fVxuXG5cdFx0bmV3IEpldEVuZ2luZVJlbmRlckFkdmFuY2VkRGF0ZUZpZWxkKFxuXHRcdFx0dGhpcy4kaW5wdXQuY2xvc2VzdCggJy5qZmItYWR2YW5jZWQtZGF0ZScgKSxcblx0XHRcdHtcblx0XHRcdFx0bGFiZWxzOiBsYWJlbHMsXG5cdFx0XHRcdHRlbXBsYXRlRW5naW5lOiBjcmVhdGVUZW1wbGF0ZUVuZ2luZSgpXG5cdFx0XHR9XG5cdFx0KTtcblx0fTtcblxuXHR0aGlzLnNldFZhbHVlID0gZnVuY3Rpb24oIG5ld1ZhbHVlICkge1xuXHRcdG5ld1ZhbHVlID0gbmV3VmFsdWUgfHwgbnVsbDtcblx0XHR0aGlzLmNhbGNWYWx1ZSAgICAgPSBuZXdWYWx1ZTtcblx0XHR0aGlzLnZhbHVlLmN1cnJlbnQgPSBuZXdWYWx1ZTtcblx0fTtcbn1cblxuSmV0RW5naW5lQWR2YW5jZWREYXRlLnByb3RvdHlwZSA9IE9iamVjdC5jcmVhdGUoIElucHV0RGF0YS5wcm90b3R5cGUgKTtcblxuYWRkRmlsdGVyKFxuXHQnamV0LmZiLmlucHV0cycsXG5cdCdqZXQtZm9ybS1idWlsZGVyL3NpZ25hdHVyZS1maWVsZCcsXG5cdGZ1bmN0aW9uICggaW5wdXRzICkge1xuXHRcdGlucHV0cyA9IFsgSmV0RW5naW5lQWR2YW5jZWREYXRlLCAuLi5pbnB1dHMgXTtcblxuXHRcdHJldHVybiBpbnB1dHM7XG5cdH0sXG4pOyJdLCJuYW1lcyI6W10sInNvdXJjZVJvb3QiOiIifQ==