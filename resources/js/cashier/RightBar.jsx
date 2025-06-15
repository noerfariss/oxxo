import { Button, Card, Col, List } from 'antd'
import React from 'react'
import { Colors } from '../utils/Colors'
import { CalendarOutlined, PercentageOutlined, RightCircleTwoTone, TagsTwoTone, UserOutlined } from '@ant-design/icons'

export const RightBar = () => {
    return (
        <div style={{ height: '100vh', backgroundColor: Colors.primary, paddingRight: 22, paddingLeft: 22, paddingTop: 14, paddingBottom: 14, display: 'flex', flexDirection: 'column' }}>
            <section style={{ textAlign: 'right' }}>
                <h3 style={{ padding: 0, margin: 0, fontWeight: 'normal', color: Colors.gray50 }}>Total</h3>
                <h2 style={{ color: Colors.yellow, fontSize: 32, margin: 0, padding: 0, fontWeight: 500 }}>Rp 345.000</h2>
            </section>

            {/* list  */}
            <h3 style={{ marginTop: 22, fontWeight: 500, color: Colors.yellow, letterSpacing: .5 }}><TagsTwoTone /> Detail transaksi</h3>
            <section style={{ flex: 1, height: 320, overflowY: 'scroll' }}>
                <div style={{ borderWidth: 1, borderColor: Colors.blue700, borderStyle: 'solid', borderRadius: 8, padding: 8, display: 'flex', flexDirection: 'row', justifyContent: 'space-between', cursor: 'pointer', position: 'relative', marginBottom: 12 }}>
                    <div style={{ flex: 2 }}>
                        <h5 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold', fontSize: 11, letterSpacing: .5 }}>APPAREL</h5>
                        <h4 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', letterSpacing: .5 }}>Mini wedding gown</h4>
                        <small style={{ color: Colors.gray50, fontSize: 11, fontStyle: 'italic' }}>- Uk. 2 x 3.2 Meter</small>
                    </div>
                    <div style={{ backgroundColor: Colors.yellow, width: 20, height: 20, fontSize: 14, fontWeight: 'bold', textAlign: "center", borderRadius: 10, marginTop: 18 }}>4</div>
                    <div style={{ textAlign: 'right', flex: 1 }}>
                        <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', fontSize: 11, letterSpacing: .5 }}>DC/LC</h6>
                        <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold' }}>Rp 65.000</h6>
                    </div>
                </div>

                <div style={{ borderWidth: 1, borderColor: Colors.blue700, borderStyle: 'solid', borderRadius: 8, padding: 8, display: 'flex', flexDirection: 'row', justifyContent: 'space-between', cursor: 'pointer', position: 'relative', marginBottom: 12 }}>
                    <div style={{ flex: 2 }}>
                        <h5 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold', fontSize: 11, letterSpacing: .5 }}>APPAREL</h5>
                        <h4 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', letterSpacing: .5 }}>Mini wedding gown</h4>
                        <small style={{ color: Colors.gray50, fontSize: 11, fontStyle: 'italic' }}>- Uk. 2 x 3.2 Meter</small>
                    </div>
                    <div style={{ backgroundColor: Colors.yellow, width: 20, height: 20, fontSize: 14, fontWeight: 'bold', textAlign: "center", borderRadius: 10, marginTop: 18 }}>4</div>
                    <div style={{ textAlign: 'right', flex: 1 }}>
                        <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', fontSize: 11, letterSpacing: .5 }}>DC/LC</h6>
                        <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold' }}>Rp 65.000</h6>
                    </div>
                </div>

                <div style={{ borderWidth: 1, borderColor: Colors.blue700, borderStyle: 'solid', borderRadius: 8, padding: 8, display: 'flex', flexDirection: 'row', justifyContent: 'space-between', cursor: 'pointer', position: 'relative', marginBottom: 12 }}>
                    <div style={{ flex: 2 }}>
                        <h5 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold', fontSize: 11, letterSpacing: .5 }}>APPAREL</h5>
                        <h4 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', letterSpacing: .5 }}>Mini wedding gown</h4>
                        <small style={{ color: Colors.gray50, fontSize: 11, fontStyle: 'italic' }}>- Uk. 2 x 3.2 Meter</small>
                    </div>
                    <div style={{ backgroundColor: Colors.yellow, width: 20, height: 20, fontSize: 14, fontWeight: 'bold', textAlign: "center", borderRadius: 10, marginTop: 18 }}>4</div>
                    <div style={{ textAlign: 'right', flex: 1 }}>
                        <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', fontSize: 11, letterSpacing: .5 }}>DC/LC</h6>
                        <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold' }}>Rp 65.000</h6>
                    </div>
                </div>


            </section>

            {/* --------------- tombol & footer ------------------- */}
            <section>
                <h3 style={{ fontWeight: 500, color: Colors.yellow, letterSpacing: .5, display:'flex', justifyContent:'space-between', alignItems:'center' }}>
                    <div>
                        <TagsTwoTone /> Informasi
                    </div>
                    <button type='button' style={{ border: 0, background: 'none', color: Colors.gray50, letterSpacing:.5, fontSize:13, borderWidth:1, borderStyle:'solid', borderColor:Colors.gray100, borderRadius:4 }}>Edit <RightCircleTwoTone /></button>
                </h3>
                <section style={{ marginBottom: 22 }}>
                    <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 8, paddingTop: 8 }}>
                        <h4 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', letterSpacing: .5 }}><PercentageOutlined /> Diskon</h4>
                        <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold' }}>10%</h6>
                    </div>
                    <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 8, paddingTop: 8 }}>
                        <h4 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', letterSpacing: .5 }}><CalendarOutlined /> Diambil pada tanggal</h4>
                        <h6 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'bold' }}>20 Juni 2025</h6>
                    </div>
                    <div style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderColor: Colors.gray100, paddingBottom: 8, paddingTop: 8 }}>
                        <h4 style={{ margin: 0, padding: 0, color: Colors.gray50, fontWeight: 'normal', letterSpacing: .5 }}><UserOutlined /> Member</h4>
                        <h6 style={{ margin: 0, padding: 0, color: Colors.yellow, fontWeight: 'bold', textTransform: 'uppercase' }}>nur faris prastyo</h6>
                    </div>
                </section>

                <button
                    type="button"
                    style={{
                        backgroundColor: Colors.yellow,
                        border: 0,
                        width: '100%',
                        padding: 12,
                        borderRadius: 8,
                        marginBottom:24,
                        cursor:'pointer'
                    }}
                >
                    Proses
                </button>
            </section>

        </div>
    )
}
