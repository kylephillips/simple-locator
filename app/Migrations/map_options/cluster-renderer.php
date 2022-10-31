{
  render: ({ count, position }) =>
    new google.maps.Marker({
      label: { text: String(count), color: "white", fontSize: "12px" },
      position,
      zIndex: Number(google.maps.Marker.MAX_ZINDEX) + count,
      icon: {
        path: "M-20,0a20,20 0 1,0 40,0a20,20 0 1,0 -40,0",
        fillColor: "#000000",
        fillOpacity: 1,
        anchor: new google.maps.Point(0,0),
        strokeWeight: 2,
        strokeColor: "#ffffff",
        scale: .8,
      }
    }),
};