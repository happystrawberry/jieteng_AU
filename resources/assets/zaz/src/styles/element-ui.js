import { StyleSheet } from 'react-native';

export default StyleSheet.create({
  'el-upload input[type="file"]': {
    'display': 'none !important'
  },
  'el-upload__input': {
    'display': 'none'
  },
  'el-form-item__content': {
    'boxSizing': 'border-box'
  },
  'el-dialog': {
    'transform': 'none',
    'left': [{ 'unit': 'px', 'value': 0 }],
    'position': 'relative',
    'margin': [{ 'unit': 'px', 'value': 0 }, { 'unit': 'string', 'value': 'auto' }, { 'unit': 'px', 'value': 0 }, { 'unit': 'string', 'value': 'auto' }]
  },
  'comheader el-input el-input__inner': {
    'width': [{ 'unit': 'px', 'value': 120 }],
    'fontSize': [{ 'unit': 'px', 'value': 14 }]
  },
  'comheader other el-input__inner': {
    'width': [{ 'unit': 'px', 'value': 150 }]
  },
  'table': {
    'marginBottom': [{ 'unit': 'px', 'value': 40 }]
  },
  'table-header': {
    'display': 'flex',
    'alignItems': 'center'
  },
  'table-header div': {
    'marginLeft': [{ 'unit': 'px', 'value': 50 }]
  },
  'grid-content': {
    'fontSize': [{ 'unit': 'px', 'value': 14 }]
  },
  'h3': {
    'marginTop': [{ 'unit': 'px', 'value': 0 }],
    'marginBottom': [{ 'unit': 'px', 'value': 0 }]
  },
  'el-dialog__header': {
    'textAlign': 'center'
  },
  'el-textarea__inner': {
    'resize': 'none'
  },
  'zone-content': {
    'margin': [{ 'unit': 'px', 'value': 30 }, { 'unit': 'px', 'value': 30 }, { 'unit': 'px', 'value': 30 }, { 'unit': 'px', 'value': 30 }]
  },
  'is-in-pagination el-input__inner': {
    'width': [{ 'unit': 'px', 'value': 40 }]
  },
  'upload-container el-upload': {
    'width': [{ 'unit': '%H', 'value': 1 }]
  },
  'upload-container el-upload el-upload-dragger': {
    'width': [{ 'unit': '%H', 'value': 1 }],
    'height': [{ 'unit': 'px', 'value': 200 }]
  },
  'grid-content': {
    'boxShadow': [{ 'unit': 'px', 'value': 0 }, { 'unit': 'px', 'value': 2 }, { 'unit': 'px', 'value': 12 }, { 'unit': 'px', 'value': 0 }, { 'unit': 'string', 'value': 'rgba(0, 0, 0, 0.3)' }],
    'padding': [{ 'unit': 'px', 'value': 20 }, { 'unit': 'px', 'value': 14 }, { 'unit': 'px', 'value': 20 }, { 'unit': 'px', 'value': 14 }],
    'boxSizing': 'border-box',
    'position': 'relative',
    'marginBottom': [{ 'unit': 'px', 'value': 20 }],
    'borderRadius': '10px'
  },
  'grid-content el-checkbox': {
    'marginLeft': [{ 'unit': 'px', 'value': 10 }]
  },
  'grid-content el-tag': {
    'padding': [{ 'unit': 'px', 'value': 0 }, { 'unit': 'px', 'value': 20 }, { 'unit': 'px', 'value': 0 }, { 'unit': 'px', 'value': 20 }],
    'cursor': 'pointer'
  },
  'grid-content > div': {
    'display': 'flex',
    'justifyContent': 'space-between',
    'alignItems': 'center',
    'minHeight': [{ 'unit': 'px', 'value': 22 }]
  },
  'grid-content > divradio': {
    'position': 'relative',
    'justifyContent': 'flex-start'
  },
  'grid-content > divradio:after': {
    'content': '""',
    'display': 'block',
    'width': [{ 'unit': '%H', 'value': 1 }],
    'height': [{ 'unit': '%V', 'value': 1 }],
    'position': 'absolute',
    'top': [{ 'unit': 'px', 'value': 0 }],
    'left': [{ 'unit': 'px', 'value': 0 }],
    'zIndex': '11'
  },
  'grid-content > divradio > span': {
    'marginRight': [{ 'unit': 'px', 'value': 30 }]
  },
  'grid-content > div tips': {
    'color': '#ff1717'
  },
  'grid-content margin': {
    'marginBottom': [{ 'unit': 'px', 'value': 20 }]
  },
  'grid-content list-level': {
    'position': 'relative',
    'paddingTop': [{ 'unit': 'px', 'value': 10 }],
    'width': [{ 'unit': '%H', 'value': 0.95 }]
  },
  'grid-content list-level p': {
    'position': 'absolute',
    'color': '#66b1ff',
    'top': [{ 'unit': 'px', 'value': -30 }],
    'right': [{ 'unit': 'px', 'value': 0 }]
  },
  'grid-content list-info': {
    'position': 'absolute',
    'width': [{ 'unit': 'px', 'value': 70 }],
    'top': [{ 'unit': 'px', 'value': 10 }],
    'right': [{ 'unit': 'px', 'value': 14 }]
  },
  'grid-content list-info-type': {
    'width': [{ 'unit': 'px', 'value': 70 }],
    'height': [{ 'unit': 'px', 'value': 70 }],
    'lineHeight': [{ 'unit': 'px', 'value': 70 }],
    'fontSize': [{ 'unit': 'px', 'value': 12 }],
    'textAlign': 'center',
    'border': [{ 'unit': 'px', 'value': 1 }, { 'unit': 'string', 'value': 'solid' }, { 'unit': 'string', 'value': '#bbbbbb' }],
    'borderRadius': '70px',
    'marginBottom': [{ 'unit': 'px', 'value': 14 }]
  },
  'grid-content list-info-num': {
    'textAlign': 'center',
    'display': 'block'
  }
});
