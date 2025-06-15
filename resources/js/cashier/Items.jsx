import { Card, Col, Row } from 'antd'
import React, { useEffect, useState } from 'react'
import { Colors } from '../utils/Colors';

const Items = ({ datas = [] }) => {
    return (
        <div style={{ height: 540, overflowY: 'scroll', overflowX: 'hidden' }}>
            <Row gutter={[8, 8]}>
                {
                    datas.map((val, i) => {
                        return (
                            <Col key={i} xs={12} sm={8} xl={6}>
                                <Card style={{ cursor: 'pointer' }}>
                                    <h5 style={{ margin: 0, padding: 0, fontWeight: 'bold', fontSize: 11, letterSpacing: .5 }}>{val.category.name}</h5>
                                    <h4 style={{ margin: 0, padding: 0, fontWeight: 'normal', letterSpacing: .5 }}>{val.name}</h4>
                                    <div style={{ display: 'flex', alignItems: 'center', gap: 4 }}>
                                        <h6 style={{ margin: 0, padding: 0, fontWeight: 'normal', fontSize: 12, letterSpacing: .5, color: Colors.primary }}>DC/LC</h6>
                                        <h6 style={{ margin: 0, padding: 0, fontWeight: 'bold', fontSize: 12, color: Colors.primary }}>Rp 65.000</h6>
                                    </div>
                                </Card>
                            </Col>
                        )
                    })
                }
            </Row>
        </div>

    )
}

export default Items
