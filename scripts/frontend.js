// Parent element
var mountNode = document.getElementById('example');

// React config
var HelloMessage = React.createClass({displayName: "HelloMessage",
  render: function() {
    return React.createElement("div", null, "Hello ", this.props.name);
  }
});

// React rendering
React.render(React.createElement(HelloMessage, {name: "John"}), mountNode);